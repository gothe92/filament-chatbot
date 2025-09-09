<?php

namespace FilamentChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'tokens_used',
        'metadata',
    ];

    protected $casts = [
        'tokens_used' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the conversation
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }

    /**
     * Check if message is from user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if message is from assistant
     */
    public function isAssistant(): bool
    {
        return $this->role === 'assistant';
    }

    /**
     * Check if message is system message
     */
    public function isSystem(): bool
    {
        return $this->role === 'system';
    }

    /**
     * Get word count
     */
    public function getWordCount(): int
    {
        return str_word_count($this->content);
    }

    /**
     * Get character count
     */
    public function getCharacterCount(): int
    {
        return strlen($this->content);
    }

    /**
     * Scope for role
     */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for user messages
     */
    public function scopeUser($query)
    {
        return $query->role('user');
    }

    /**
     * Scope for assistant messages
     */
    public function scopeAssistant($query)
    {
        return $query->role('assistant');
    }

    /**
     * Get formatted content (with markdown parsing if needed)
     */
    public function getFormattedContent(): string
    {
        // This could be enhanced with markdown parsing
        return nl2br(e($this->content));
    }
}
