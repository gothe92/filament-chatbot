<?php

namespace FilamentChatbot\Services;

use FilamentChatbot\Models\ChatbotResource;
use FilamentChatbot\Models\ChatbotConversation;
use FilamentChatbot\Models\ChatbotMessage;
use FilamentChatbot\Models\ChatbotDocument;
use FilamentChatbot\Models\ChatbotPredefinedQuestion;
use App\Services\RAGService;
use App\Services\AIServiceFactory;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    protected $ragService;
    protected $aiService;

    public function __construct()
    {
        // Use existing RAG service if available, otherwise create a basic one
        $this->ragService = app()->bound(RAGService::class) 
            ? app(RAGService::class) 
            : null;
            
        $this->aiService = app()->bound('App\Services\AIServiceFactory')
            ? AIServiceFactory::create()
            : null;
    }

    /**
     * Generate response for chatbot
     */
    public function generateResponse(
        string $message,
        ChatbotResource $chatbotResource,
        int $conversationId,
        string $language = 'hu'
    ): array {
        try {
            // First check predefined questions
            $predefinedResponse = $this->checkPredefinedQuestions($message, $chatbotResource);
            if ($predefinedResponse) {
                return $predefinedResponse;
            }

            // Get conversation history
            $history = $this->getConversationHistory($conversationId);

            // Generate response based on RAG mode
            switch ($chatbotResource->rag_mode) {
                case ChatbotResource::RAG_MODE_DOCUMENTS_ONLY:
                    return $this->generateDocumentsOnlyResponse($message, $chatbotResource, $history, $language);
                
                case ChatbotResource::RAG_MODE_DOCUMENTS_AND_AI:
                    return $this->generateDocumentsAndAIResponse($message, $chatbotResource, $history, $language);
                
                case ChatbotResource::RAG_MODE_AI_ONLY:
                    return $this->generateAIOnlyResponse($message, $chatbotResource, $history, $language);
                
                case ChatbotResource::RAG_MODE_ALL_DOCUMENTS:
                    return $this->generateAllDocumentsResponse($message, $chatbotResource, $history, $language);
                
                default:
                    return $this->generateDocumentsAndAIResponse($message, $chatbotResource, $history, $language);
            }
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage(), [
                'message' => $message,
                'chatbot_resource_id' => $chatbotResource->id,
                'conversation_id' => $conversationId,
            ]);

            return [
                'success' => false,
                'content' => __('filament-chatbot::messages.error_generating_response'),
                'tokens_used' => 0,
            ];
        }
    }

    /**
     * Check predefined questions
     */
    protected function checkPredefinedQuestions(string $message, ChatbotResource $chatbotResource): ?array
    {
        $questions = $chatbotResource->activePredefinedQuestions()->get();
        
        foreach ($questions as $question) {
            // Simple similarity check
            $similarity = $this->calculateSimilarity($message, $question->question);
            if ($similarity > 0.8) {
                return [
                    'success' => true,
                    'content' => $question->answer,
                    'tokens_used' => 0,
                    'used_rag' => false,
                    'from_predefined' => true,
                    'question_id' => $question->id,
                ];
            }
        }

        return null;
    }

    /**
     * Generate response using only documents
     */
    protected function generateDocumentsOnlyResponse(
        string $message,
        ChatbotResource $chatbotResource,
        array $history,
        string $language
    ): array {
        if (!$this->ragService) {
            return $this->createErrorResponse('RAG service not available');
        }

        $relevantDocuments = $this->findRelevantDocuments($message, $chatbotResource);
        
        if ($relevantDocuments->isEmpty()) {
            return [
                'success' => true,
                'content' => __('filament-chatbot::messages.no_documents_found', [], $language),
                'tokens_used' => 0,
                'used_rag' => true,
                'rag_documents' => [],
                'no_answer_found' => true,
            ];
        }

        return $this->generateAIResponse($message, $chatbotResource, $history, $relevantDocuments, $language, true);
    }

    /**
     * Generate response using documents and AI
     */
    protected function generateDocumentsAndAIResponse(
        string $message,
        ChatbotResource $chatbotResource,
        array $history,
        string $language
    ): array {
        $relevantDocuments = $this->findRelevantDocuments($message, $chatbotResource);
        
        return $this->generateAIResponse($message, $chatbotResource, $history, $relevantDocuments, $language, false);
    }

    /**
     * Generate response using only AI
     */
    protected function generateAIOnlyResponse(
        string $message,
        ChatbotResource $chatbotResource,
        array $history,
        string $language
    ): array {
        return $this->generateAIResponse($message, $chatbotResource, $history, collect(), $language, false);
    }

    /**
     * Generate response using all documents
     */
    protected function generateAllDocumentsResponse(
        string $message,
        ChatbotResource $chatbotResource,
        array $history,
        string $language
    ): array {
        $allDocuments = $chatbotResource->documents()->get();
        
        return $this->generateAIResponse($message, $chatbotResource, $history, $allDocuments, $language, false);
    }

    /**
     * Generate AI response
     */
    protected function generateAIResponse(
        string $message,
        ChatbotResource $chatbotResource,
        array $history,
        $documents,
        string $language,
        bool $documentsOnly = false
    ): array {
        if (!$this->aiService) {
            return $this->createErrorResponse('AI service not available');
        }

        $systemPrompt = $this->buildSystemPrompt($chatbotResource, $documents, $language, $documentsOnly);
        
        $response = $this->aiService->chat($message, $history, $systemPrompt);
        
        if ($response['success']) {
            $response['used_rag'] = !$documents->isEmpty();
            $response['rag_documents'] = $documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                ];
            })->toArray();
        }

        return $response;
    }

    /**
     * Find relevant documents
     */
    protected function findRelevantDocuments(string $message, ChatbotResource $chatbotResource)
    {
        if ($this->ragService) {
            // Use existing RAG service if available
            return $this->ragService->findRelevantDocuments($message, $chatbotResource->id);
        }

        // Simple keyword search fallback
        $documents = $chatbotResource->documents()->get();
        
        return $documents->filter(function ($document) use ($message) {
            $messageWords = explode(' ', strtolower($message));
            $content = strtolower($document->title . ' ' . $document->content);
            
            $matches = 0;
            foreach ($messageWords as $word) {
                if (strlen($word) > 2 && strpos($content, $word) !== false) {
                    $matches++;
                }
            }
            
            return $matches > 0;
        });
    }

    /**
     * Build system prompt
     */
    protected function buildSystemPrompt(
        ChatbotResource $chatbotResource,
        $documents,
        string $language,
        bool $documentsOnly = false
    ): string {
        $resourceable = $chatbotResource->resourceable;
        $name = $resourceable->name ?? $resourceable->title ?? 'Item';
        
        $prompt = $language === 'hu' 
            ? "Segítőkész asszisztens vagy, aki információt nyújt erről: {$name}."
            : "You are a helpful assistant providing information about: {$name}.";

        if ($resourceable->description ?? false) {
            $prompt .= "\n\n" . ($language === 'hu' ? 'Leírás: ' : 'Description: ') . $resourceable->description;
        }

        if ($documentsOnly) {
            $prompt .= "\n\n" . ($language === 'hu' 
                ? "FONTOS: Kizárólag a mellékelt dokumentumok alapján válaszolj. Ha az információ nem található a dokumentumokban, akkor mondd meg, hogy nem található releváns információ."
                : "IMPORTANT: Only answer based on the provided documents. If information is not found in the documents, say that no relevant information is available.");
        }

        if (!$documents->isEmpty()) {
            $prompt .= "\n\n" . ($language === 'hu' ? 'Elérhető dokumentumok:' : 'Available documents:') . "\n";
            
            foreach ($documents as $document) {
                $prompt .= "\nDokumentum: {$document->title}\n{$document->content}\n";
            }
        }

        return $prompt;
    }

    /**
     * Get conversation history
     */
    protected function getConversationHistory(int $conversationId): array
    {
        $messages = ChatbotMessage::where('conversation_id', $conversationId)
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        return $messages->map(function ($message) {
            return [
                'role' => $message->role,
                'content' => $message->content,
            ];
        })->toArray();
    }

    /**
     * Calculate simple similarity between two strings
     */
    protected function calculateSimilarity(string $str1, string $str2): float
    {
        similar_text(strtolower($str1), strtolower($str2), $percent);
        return $percent / 100;
    }

    /**
     * Create error response
     */
    protected function createErrorResponse(string $error): array
    {
        return [
            'success' => false,
            'content' => __('filament-chatbot::messages.error_generating_response'),
            'tokens_used' => 0,
            'error' => $error,
        ];
    }
}