<?php

namespace FilamentChatbot\Traits;

use FilamentChatbot\Models\ChatbotResource;
use FilamentChatbot\Models\ChatbotDocument;
use FilamentChatbot\Models\ChatbotConversation;
use FilamentChatbot\Models\ChatbotPredefinedQuestion;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

trait HasChatbot
{
    /**
     * Get the chatbot resource for this model
     */
    public function chatbotResource(): MorphOne
    {
        return $this->morphOne(ChatbotResource::class, 'resourceable');
    }
    
    /**
     * Get all chatbot documents through the resource
     */
    public function chatbotDocuments()
    {
        return $this->hasManyThrough(
            ChatbotDocument::class,
            ChatbotResource::class,
            'resourceable_id',
            'chatbot_resource_id',
            'id',
            'id'
        )->where('chatbot_resources.resourceable_type', get_class($this));
    }
    
    /**
     * Get all chatbot conversations
     */
    public function chatbotConversations()
    {
        return $this->hasManyThrough(
            ChatbotConversation::class,
            ChatbotResource::class,
            'resourceable_id',
            'chatbot_resource_id',
            'id',
            'id'
        )->where('chatbot_resources.resourceable_type', get_class($this));
    }
    
    /**
     * Get all predefined questions
     */
    public function chatbotPredefinedQuestions()
    {
        return $this->hasManyThrough(
            ChatbotPredefinedQuestion::class,
            ChatbotResource::class,
            'resourceable_id',
            'chatbot_resource_id',
            'id',
            'id'
        )->where('chatbot_resources.resourceable_type', get_class($this));
    }
    
    /**
     * Check if model has chatbot enabled
     */
    public function hasChatbot(): bool
    {
        return $this->chatbotResource()->exists() && $this->chatbotResource->active;
    }
    
    /**
     * Enable chatbot for this model
     */
    public function enableChatbot(array $settings = []): ChatbotResource
    {
        return $this->chatbotResource()->updateOrCreate(
            [],
            array_merge([
                'active' => true,
                'rag_mode' => config('filament-chatbot.defaults.rag_mode', 'documents_and_ai'),
                'metadata' => [],
            ], $settings)
        );
    }
    
    /**
     * Disable chatbot for this model
     */
    public function disableChatbot(): bool
    {
        if ($resource = $this->chatbotResource) {
            return $resource->update(['active' => false]);
        }
        
        return false;
    }
    
    /**
     * Get chatbot settings
     */
    public function getChatbotSettings(): ?array
    {
        if (!$this->hasChatbot()) {
            return null;
        }
        
        $resource = $this->chatbotResource;
        
        return [
            'rag_mode' => $resource->rag_mode,
            'chatbot_setting_id' => $resource->chatbot_setting_id,
            'metadata' => $resource->metadata,
            'active' => $resource->active,
        ];
    }
    
    /**
     * Update chatbot settings
     */
    public function updateChatbotSettings(array $settings): bool
    {
        if (!$this->hasChatbot()) {
            $this->enableChatbot($settings);
            return true;
        }
        
        return $this->chatbotResource->update($settings);
    }
    
    /**
     * Add a document to chatbot
     */
    public function addChatbotDocument(string $title, string $content, array $metadata = []): ChatbotDocument
    {
        if (!$this->hasChatbot()) {
            $this->enableChatbot();
        }
        
        return $this->chatbotResource->documents()->create([
            'title' => $title,
            'content' => $content,
            'metadata' => $metadata,
        ]);
    }
    
    /**
     * Add a predefined question
     */
    public function addPredefinedQuestion(string $question, string $answer, int $order = 0): ChatbotPredefinedQuestion
    {
        if (!$this->hasChatbot()) {
            $this->enableChatbot();
        }
        
        return $this->chatbotResource->predefinedQuestions()->create([
            'question' => $question,
            'answer' => $answer,
            'order' => $order,
            'active' => true,
        ]);
    }
    
    /**
     * Get active predefined questions
     */
    public function getActivePredefinedQuestions()
    {
        return $this->chatbotPredefinedQuestions()
            ->where('active', true)
            ->orderBy('order')
            ->get();
    }
    
    /**
     * Generate system prompt for chatbot
     */
    public function generateChatbotSystemPrompt(string $language = 'hu'): string
    {
        $name = $this->name ?? $this->title ?? 'Item';
        $description = $this->description ?? '';
        
        $prompt = match($language) {
            'en' => "You are a helpful assistant providing information about: {$name}.",
            'hu' => "Segítőkész asszisztens vagy, aki információt nyújt erről: {$name}.",
            default => "You are a helpful assistant providing information about: {$name}.",
        };
        
        if ($description) {
            $prompt .= "\n\n" . match($language) {
                'en' => "Description: {$description}",
                'hu' => "Leírás: {$description}",
                default => "Description: {$description}",
            };
        }
        
        return $prompt;
    }
}