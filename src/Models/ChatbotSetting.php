<?php

namespace FilamentChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotSetting extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_default',
        'personality',
        'tone',
        'expertise_level',
        'response_length',
        'temperature',
        'forbidden_topics',
        'avoided_expressions',
        'behavioral_rules',
        'custom_instructions',
        'active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'response_length' => 'integer',
        'temperature' => 'decimal:2',
        'forbidden_topics' => 'array',
        'avoided_expressions' => 'array',
        'active' => 'boolean',
    ];

    // Personality constants
    const PERSONALITY_HELPFUL = 'helpful';

    const PERSONALITY_PROFESSIONAL = 'professional';

    const PERSONALITY_FRIENDLY = 'friendly';

    const PERSONALITY_SCHOLARLY = 'scholarly';

    // Tone constants
    const TONE_PROFESSIONAL = 'professional';

    const TONE_CASUAL = 'casual';

    const TONE_FORMAL = 'formal';

    const TONE_ENTHUSIASTIC = 'enthusiastic';

    // Expertise level constants
    const EXPERTISE_BASIC = 'basic';

    const EXPERTISE_INTERMEDIATE = 'intermediate';

    const EXPERTISE_ADVANCED = 'advanced';

    const EXPERTISE_EXPERT = 'expert';

    public static function getPersonalities(): array
    {
        return [
            self::PERSONALITY_HELPFUL => __('filament-chatbot::messages.personalities.helpful'),
            self::PERSONALITY_PROFESSIONAL => __('filament-chatbot::messages.personalities.professional'),
            self::PERSONALITY_FRIENDLY => __('filament-chatbot::messages.personalities.friendly'),
            self::PERSONALITY_SCHOLARLY => __('filament-chatbot::messages.personalities.scholarly'),
        ];
    }

    public static function getTones(): array
    {
        return [
            self::TONE_PROFESSIONAL => __('filament-chatbot::messages.tones.professional'),
            self::TONE_CASUAL => __('filament-chatbot::messages.tones.casual'),
            self::TONE_FORMAL => __('filament-chatbot::messages.tones.formal'),
            self::TONE_ENTHUSIASTIC => __('filament-chatbot::messages.tones.enthusiastic'),
        ];
    }

    public static function getExpertiseLevels(): array
    {
        return [
            self::EXPERTISE_BASIC => __('filament-chatbot::messages.expertise.basic'),
            self::EXPERTISE_INTERMEDIATE => __('filament-chatbot::messages.expertise.intermediate'),
            self::EXPERTISE_ADVANCED => __('filament-chatbot::messages.expertise.advanced'),
            self::EXPERTISE_EXPERT => __('filament-chatbot::messages.expertise.expert'),
        ];
    }

    /**
     * Get chatbot resources using this setting
     */
    public function chatbotResources(): HasMany
    {
        return $this->hasMany(ChatbotResource::class);
    }

    /**
     * Get the default setting
     */
    public static function getDefault(): ?self
    {
        return self::where('is_default', true)->where('active', true)->first();
    }

    /**
     * Set this as the default setting
     */
    public function setAsDefault(): bool
    {
        // Remove default from other settings
        self::where('is_default', true)->update(['is_default' => false]);

        // Set this as default
        return $this->update(['is_default' => true]);
    }

    /**
     * Get personality label
     */
    public function getPersonalityLabel(): string
    {
        return self::getPersonalities()[$this->personality] ?? $this->personality;
    }

    /**
     * Get tone label
     */
    public function getToneLabel(): string
    {
        return self::getTones()[$this->tone] ?? $this->tone;
    }

    /**
     * Get expertise level label
     */
    public function getExpertiseLevelLabel(): string
    {
        return self::getExpertiseLevels()[$this->expertise_level] ?? $this->expertise_level;
    }

    /**
     * Check if this is a system default setting
     */
    public function isSystemDefault(): bool
    {
        return $this->is_default && $this->active;
    }

    /**
     * Scope for active settings
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope for default settings
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
