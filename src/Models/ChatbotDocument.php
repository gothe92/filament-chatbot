<?php

namespace FilamentChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotDocument extends Model
{
    protected $fillable = [
        'chatbot_resource_id',
        'title',
        'content',
        'file_path',
        'file_type',
        'embedding',
        'chunks_count',
        'tokens_count',
        'metadata',
    ];

    protected $casts = [
        'embedding' => 'array',
        'chunks_count' => 'integer',
        'tokens_count' => 'integer',
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
     * Get document chunks
     */
    public function chunks(): HasMany
    {
        return $this->hasMany(ChatbotDocumentChunk::class, 'document_id');
    }

    /**
     * Check if document has embedding
     */
    public function hasEmbedding(): bool
    {
        return !empty($this->embedding);
    }

    /**
     * Check if document has chunks
     */
    public function hasChunks(): bool
    {
        return $this->chunks_count > 0;
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeAttribute(): ?string
    {
        if (!$this->file_path || !file_exists(storage_path('app/' . $this->file_path))) {
            return null;
        }

        $bytes = filesize(storage_path('app/' . $this->file_path));
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get word count
     */
    public function getWordCount(): int
    {
        return str_word_count($this->content ?? '');
    }

    /**
     * Scope for documents with embeddings
     */
    public function scopeWithEmbedding($query)
    {
        return $query->whereNotNull('embedding');
    }

    /**
     * Scope for documents with chunks
     */
    public function scopeWithChunks($query)
    {
        return $query->where('chunks_count', '>', 0);
    }
}