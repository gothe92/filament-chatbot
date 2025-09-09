<?php

namespace FilamentChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotConversation extends Model
{
    protected $fillable = [
        'chatbot_resource_id',
        'session_id',
        'language',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the chatbot resource
     */
    public function chatbotResource(): BelongsTo
    {
        return $this->belongsTo(ChatbotResource::class);
    }

    /**
     * Get all messages
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class, 'conversation_id');
    }

    /**
     * Get user messages
     */
    public function userMessages(): HasMany
    {
        return $this->messages()->where('role', 'user');
    }

    /**
     * Get assistant messages
     */
    public function assistantMessages(): HasMany
    {
        return $this->messages()->where('role', 'assistant');
    }

    /**
     * Get total tokens used
     */
    public function getTotalTokensUsed(): int
    {
        return $this->messages()->sum('tokens_used');
    }

    /**
     * Get conversation duration in minutes
     */
    public function getDurationInMinutes(): int
    {
        if ($this->messages()->count() < 2) {
            return 0;
        }

        $firstMessage = $this->messages()->orderBy('created_at', 'asc')->first();
        $lastMessage = $this->messages()->orderBy('created_at', 'desc')->first();

        return $firstMessage->created_at->diffInMinutes($lastMessage->created_at);
    }

    /**
     * Get conversation summary
     */
    public function getSummary(): array
    {
        return [
            'total_messages' => $this->messages()->count(),
            'user_messages' => $this->userMessages()->count(),
            'assistant_messages' => $this->assistantMessages()->count(),
            'total_tokens' => $this->getTotalTokensUsed(),
            'duration_minutes' => $this->getDurationInMinutes(),
            'language' => $this->language,
            'started_at' => $this->created_at,
            'last_message_at' => $this->messages()->latest()->first()?->created_at,
        ];
    }

    /**
     * Scope for session
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope for active conversations (had activity in last 30 minutes)
     */
    public function scopeActive($query)
    {
        return $query->whereHas('messages', function ($q) {
            $q->where('created_at', '>=', now()->subMinutes(30));
        });
    }

    /**
     * Clear old messages (keep last N messages)
     */
    public function trimMessages(int $keepLast = 50): void
    {
        $messagesToKeep = $this->messages()
            ->orderBy('created_at', 'desc')
            ->take($keepLast)
            ->pluck('id');

        $this->messages()
            ->whereNotIn('id', $messagesToKeep)
            ->delete();
    }
}
