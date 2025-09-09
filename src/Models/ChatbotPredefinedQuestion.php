<?php

namespace FilamentChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotPredefinedQuestion extends Model
{
    protected $fillable = [
        'chatbot_resource_id',
        'question',
        'answer',
        'active',
        'order',
        'metadata',
    ];

    protected $casts = [
        'active' => 'boolean',
        'order' => 'integer',
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
     * Scope for active questions
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope for ordered questions
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Toggle active state
     */
    public function toggleActive(): bool
    {
        $this->active = !$this->active;
        return $this->save();
    }

    /**
     * Move up in order
     */
    public function moveUp(): bool
    {
        if ($this->order <= 0) {
            return false;
        }

        $previousQuestion = self::where('chatbot_resource_id', $this->chatbot_resource_id)
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousQuestion) {
            $tempOrder = $this->order;
            $this->order = $previousQuestion->order;
            $previousQuestion->order = $tempOrder;
            
            $previousQuestion->save();
            return $this->save();
        }

        $this->order--;
        return $this->save();
    }

    /**
     * Move down in order
     */
    public function moveDown(): bool
    {
        $nextQuestion = self::where('chatbot_resource_id', $this->chatbot_resource_id)
            ->where('order', '>', $this->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextQuestion) {
            $tempOrder = $this->order;
            $this->order = $nextQuestion->order;
            $nextQuestion->order = $tempOrder;
            
            $nextQuestion->save();
            return $this->save();
        }

        $this->order++;
        return $this->save();
    }
}