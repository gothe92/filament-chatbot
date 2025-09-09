<?php

namespace FilamentChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotDocumentChunk extends Model
{
    protected $fillable = [
        'document_id',
        'content',
        'embedding',
        'chunk_index',
        'tokens_count',
        'metadata',
    ];

    protected $casts = [
        'embedding' => 'array',
        'chunk_index' => 'integer',
        'tokens_count' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the parent document
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(ChatbotDocument::class, 'document_id');
    }

    /**
     * Check if chunk has embedding
     */
    public function hasEmbedding(): bool
    {
        return !empty($this->embedding);
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
     * Scope for chunks with embeddings
     */
    public function scopeWithEmbedding($query)
    {
        return $query->whereNotNull('embedding');
    }

    /**
     * Scope for chunks by index range
     */
    public function scopeIndexRange($query, int $start, int $end)
    {
        return $query->whereBetween('chunk_index', [$start, $end]);
    }

    /**
     * Get neighboring chunks
     */
    public function getNeighbors(int $windowSize = 1)
    {
        return self::where('document_id', $this->document_id)
            ->indexRange(
                max(0, $this->chunk_index - $windowSize),
                $this->chunk_index + $windowSize
            )
            ->orderBy('chunk_index')
            ->get();
    }
}