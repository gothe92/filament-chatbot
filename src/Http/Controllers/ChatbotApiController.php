<?php

namespace FilamentChatbot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use FilamentChatbot\Models\ChatbotResource;
use FilamentChatbot\Models\ChatbotConversation;
use FilamentChatbot\Models\ChatbotMessage;
use FilamentChatbot\Services\ChatbotService;
use Illuminate\Support\Str;

class ChatbotApiController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Send a message to the chatbot
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'chatbot_resource_id' => 'required|exists:chatbot_resources,id',
            'session_id' => 'nullable|string|max:100',
        ]);

        $chatbotResource = ChatbotResource::findOrFail($request->chatbot_resource_id);
        
        if (!$chatbotResource->active) {
            return response()->json([
                'error' => __('filament-chatbot::messages.chatbot_disabled')
            ], 403);
        }

        $sessionId = $request->session_id ?? Str::uuid()->toString();

        // Find or create conversation
        $conversation = ChatbotConversation::firstOrCreate([
            'chatbot_resource_id' => $chatbotResource->id,
            'session_id' => $sessionId,
        ], [
            'language' => app()->getLocale(),
        ]);

        // Save user message
        $userMessage = ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $request->message,
            'tokens_used' => 0,
        ]);

        // Generate response
        $response = $this->chatbotService->generateResponse(
            $request->message,
            $chatbotResource,
            $conversation->id,
            app()->getLocale()
        );

        if (!$response['success']) {
            return response()->json([
                'error' => $response['content'] ?? __('filament-chatbot::messages.error_generating_response')
            ], 500);
        }

        // Save assistant message
        $assistantMessage = ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $response['content'],
            'tokens_used' => $response['tokens_used'] ?? 0,
            'metadata' => [
                'used_rag' => $response['used_rag'] ?? false,
                'rag_documents' => $response['rag_documents'] ?? [],
                'from_predefined' => $response['from_predefined'] ?? false,
            ],
        ]);

        return response()->json([
            'message' => $response['content'],
            'session_id' => $sessionId,
            'conversation_id' => $conversation->id,
            'message_id' => $assistantMessage->id,
            'tokens_used' => $response['tokens_used'] ?? 0,
            'used_rag' => $response['used_rag'] ?? false,
            'from_predefined' => $response['from_predefined'] ?? false,
        ]);
    }

    /**
     * Get conversations for a chatbot resource
     */
    public function getConversations(int $chatbotResourceId): JsonResponse
    {
        $chatbotResource = ChatbotResource::findOrFail($chatbotResourceId);

        $conversations = $chatbotResource->conversations()
            ->with(['messages' => function ($query) {
                $query->select(['id', 'conversation_id', 'role', 'created_at'])
                      ->latest()
                      ->take(1);
            }])
            ->latest()
            ->paginate(20);

        return response()->json([
            'conversations' => $conversations->items(),
            'pagination' => [
                'current_page' => $conversations->currentPage(),
                'total_pages' => $conversations->lastPage(),
                'total_items' => $conversations->total(),
            ],
        ]);
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory(string $sessionId): JsonResponse
    {
        $conversation = ChatbotConversation::where('session_id', $sessionId)->first();

        if (!$conversation) {
            return response()->json([
                'error' => __('filament-chatbot::messages.errors.conversation_not_found')
            ], 404);
        }

        $messages = $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'role' => $message->role,
                    'content' => $message->content,
                    'created_at' => $message->created_at->toISOString(),
                    'tokens_used' => $message->tokens_used,
                ];
            }),
            'conversation' => [
                'id' => $conversation->id,
                'session_id' => $conversation->session_id,
                'language' => $conversation->language,
                'created_at' => $conversation->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Clear conversation history
     */
    public function clearConversation(string $sessionId): JsonResponse
    {
        $conversation = ChatbotConversation::where('session_id', $sessionId)->first();

        if (!$conversation) {
            return response()->json([
                'error' => __('filament-chatbot::messages.errors.conversation_not_found')
            ], 404);
        }

        $conversation->messages()->delete();

        return response()->json([
            'message' => __('filament-chatbot::messages.success.conversation_cleared'),
            'session_id' => $sessionId,
        ]);
    }

    /**
     * Get predefined questions for a chatbot resource
     */
    public function getPredefinedQuestions(int $chatbotResourceId): JsonResponse
    {
        $chatbotResource = ChatbotResource::findOrFail($chatbotResourceId);

        $questions = $chatbotResource->activePredefinedQuestions()->get();

        return response()->json([
            'questions' => $questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'order' => $question->order,
                ];
            }),
            'chatbot_resource' => [
                'id' => $chatbotResource->id,
                'rag_mode' => $chatbotResource->rag_mode,
                'active' => $chatbotResource->active,
            ],
        ]);
    }
}