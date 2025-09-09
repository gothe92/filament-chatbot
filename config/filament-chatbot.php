<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Default configuration for chatbot behavior
    |
    */
    'defaults' => [
        'rag_mode' => 'documents_and_ai',
        'personality' => 'helpful',
        'tone' => 'professional',
        'expertise_level' => 'intermediate',
        'response_length' => 200,
        'temperature' => 0.7,
        'max_messages_history' => 10,
        'typing_delay' => 1000, // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Views Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which views to use for different components
    |
    */
    'views' => [
        'default' => 'filament-chatbot::livewire.chatbot-widget',
        'skeleton' => 'filament-chatbot::livewire.chatbot-widget-skeleton',
        'message' => 'filament-chatbot::components.message',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets Configuration
    |--------------------------------------------------------------------------
    |
    | Configure asset paths
    |
    */
    'assets' => [
        'js' => 'vendor/filament-chatbot/js/chatbot.js',
        'css' => 'vendor/filament-chatbot/css/chatbot.css',
        'sounds' => [
            'notification' => '/vendor/filament-chatbot/sounds/notification.mp3',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configure database settings
    |
    */
    'database' => [
        'connection' => null, // Use default connection
        'table_prefix' => 'chatbot_',
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configure AI service integration
    |
    */
    'ai' => [
        'provider' => env('CHATBOT_AI_PROVIDER', 'claude'), // claude, openai
        'model' => env('CHATBOT_AI_MODEL', 'claude-3-5-haiku-20241022'),
        'max_tokens' => env('CHATBOT_MAX_TOKENS', 1024),
        'temperature' => env('CHATBOT_TEMPERATURE', 0.7),
        
        // Claude specific
        'claude' => [
            'api_key' => env('CLAUDE_API_KEY'),
            'model' => env('CLAUDE_MODEL', 'claude-3-5-haiku-20241022'),
        ],
        
        // OpenAI specific
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Embedding Configuration
    |--------------------------------------------------------------------------
    |
    | Configure embedding service for RAG functionality
    |
    */
    'embedding' => [
        'provider' => env('CHATBOT_EMBEDDING_PROVIDER', 'openai'),
        'model' => env('CHATBOT_EMBEDDING_MODEL', 'text-embedding-3-small'),
        'cache_duration' => 60 * 60 * 24, // 24 hours
        'similarity_threshold' => 0.3,
    ],

    /*
    |--------------------------------------------------------------------------
    | RAG Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Retrieval Augmented Generation settings
    |
    */
    'rag' => [
        'chunk_size' => 500, // characters
        'chunk_overlap' => 100, // characters
        'max_chunks_per_query' => 5,
        'enable_reranking' => false,
        'context_expansion' => true,
        'expansion_window' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    |
    | Configure UI behavior and appearance
    |
    */
    'ui' => [
        'show_typing_indicator' => true,
        'show_message_timestamps' => true,
        'show_predefined_questions' => true,
        'max_predefined_questions' => 5,
        'enable_sound_notifications' => true,
        'enable_conversation_export' => true,
        'theme' => 'default', // default, minimal, modern
        'position' => 'bottom-right', // For floating widget
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Configure security settings
    |
    */
    'security' => [
        'rate_limit' => [
            'enabled' => true,
            'max_messages_per_minute' => 20,
            'max_messages_per_hour' => 100,
        ],
        'content_filtering' => [
            'enabled' => false,
            'blocked_words' => [],
            'max_message_length' => 1000,
        ],
        'session_timeout' => 30, // minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching behavior
    |
    */
    'cache' => [
        'enabled' => true,
        'driver' => env('CACHE_DRIVER', 'redis'),
        'prefix' => 'chatbot:',
        'ttl' => [
            'embeddings' => 60 * 60 * 24, // 24 hours
            'similar_questions' => 60 * 30, // 30 minutes
            'conversation_context' => 60 * 15, // 15 minutes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Configuration
    |--------------------------------------------------------------------------
    |
    | Configure analytics and logging
    |
    */
    'analytics' => [
        'enabled' => true,
        'track_user_satisfaction' => false,
        'track_response_time' => true,
        'track_token_usage' => true,
        'retention_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure background job processing
    |
    */
    'queue' => [
        'enabled' => true,
        'connection' => env('QUEUE_CONNECTION', 'redis'),
        'jobs' => [
            'process_document' => 'default',
            'generate_embeddings' => 'default',
            'cleanup_conversations' => 'low',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable/disable specific features
    |
    */
    'features' => [
        'document_upload' => true,
        'predefined_questions' => true,
        'conversation_history' => true,
        'export_conversations' => true,
        'voice_input' => false,
        'file_attachments' => false,
        'emoji_reactions' => false,
        'conversation_rating' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
    | Configure language and localization settings
    |
    */
    'localization' => [
        'default_locale' => 'hu',
        'supported_locales' => ['hu', 'en'],
        'detect_from_request' => true,
        'fallback_locale' => 'en',
    ],
];