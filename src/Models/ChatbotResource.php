<?php

namespace FilamentChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChatbotResource extends Model
{
    protected $fillable = [
        'resourceable_type',
        'resourceable_id',
        'chatbot_setting_id',
        'rag_mode',
        'active',
        'metadata',
    ];

    protected $casts = [
        'active' => 'boolean',
        'metadata' => 'array',
    ];

    // RAG mode constants
    const RAG_MODE_DOCUMENTS_ONLY = 'documents_only';

    const RAG_MODE_DOCUMENTS_AND_AI = 'documents_and_ai';

    const RAG_MODE_AI_ONLY = 'ai_only';

    const RAG_MODE_ALL_DOCUMENTS = 'all_documents';

    public static function getRagModes(): array
    {
        return [
            self::RAG_MODE_DOCUMENTS_ONLY => __('filament-chatbot::messages.rag_modes.documents_only'),
            self::RAG_MODE_DOCUMENTS_AND_AI => __('filament-chatbot::messages.rag_modes.documents_and_ai'),
            self::RAG_MODE_AI_ONLY => __('filament-chatbot::messages.rag_modes.ai_only'),
            self::RAG_MODE_ALL_DOCUMENTS => __('filament-chatbot::messages.rag_modes.all_documents'),
        ];
    }

    /**
     * Get the parent resourceable model
     */
    public function resourceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the chatbot setting
     */
    public function chatbotSetting(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ChatbotSetting::class);
    }

    /**
     * Get all documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ChatbotDocument::class);
    }

    /**
     * Get all conversations
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(ChatbotConversation::class);
    }

    /**
     * Get all predefined questions
     */
    public function predefinedQuestions(): HasMany
    {
        return $this->hasMany(ChatbotPredefinedQuestion::class);
    }

    /**
     * Get active predefined questions
     */
    public function activePredefinedQuestions(): HasMany
    {
        return $this->predefinedQuestions()->where('active', true)->orderBy('order');
    }

    /**
     * Get chatbot settings (from setting or defaults)
     */
    public function getChatbotSettings(): array
    {
        if ($this->chatbotSetting) {
            return $this->chatbotSetting->toArray();
        }

        // Return default settings
        return [
            'personality' => config('filament-chatbot.defaults.personality', 'helpful'),
            'tone' => config('filament-chatbot.defaults.tone', 'professional'),
            'expertise_level' => config('filament-chatbot.defaults.expertise_level', 'intermediate'),
            'response_length' => config('filament-chatbot.defaults.response_length', 200),
            'temperature' => config('filament-chatbot.defaults.temperature', 0.7),
        ];
    }

    /**
     * Get RAG mode label
     */
    public function getRagModeLabel(): string
    {
        return self::getRagModes()[$this->rag_mode] ?? $this->rag_mode;
    }

    /**
     * Check if RAG mode uses documents
     */
    public function usesDocuments(): bool
    {
        return in_array($this->rag_mode, [
            self::RAG_MODE_DOCUMENTS_ONLY,
            self::RAG_MODE_DOCUMENTS_AND_AI,
            self::RAG_MODE_ALL_DOCUMENTS,
        ]);
    }

    /**
     * Check if RAG mode uses AI
     */
    public function usesAI(): bool
    {
        return in_array($this->rag_mode, [
            self::RAG_MODE_DOCUMENTS_AND_AI,
            self::RAG_MODE_AI_ONLY,
        ]);
    }

    /**
     * Get conversation stats
     */
    public function getStats(): array
    {
        return [
            'total_conversations' => $this->conversations()->count(),
            'total_messages' => ChatbotMessage::whereIn('conversation_id', $this->conversations()->pluck('id'))->count(),
            'total_documents' => $this->documents()->count(),
            'total_questions' => $this->predefinedQuestions()->count(),
            'active_questions' => $this->activePredefinedQuestions()->count(),
        ];
    }
}
