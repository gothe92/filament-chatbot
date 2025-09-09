# Filament Chatbot Plugin

A powerful and flexible Filament plugin that adds chatbot functionality to any resource using morph relationships. The plugin provides customizable Livewire components with separated JavaScript and Blade views for maximum flexibility.

## Features

- 🤖 **Universal Chatbot**: Add chatbot to any model using a simple trait
- 🎨 **Customizable Views**: Separate JavaScript and Blade templates for easy customization
- 📚 **RAG Support**: Document-based responses with multiple RAG modes
- 💬 **Livewire Integration**: Real-time chat with reactive components
- 🌍 **Multi-language**: Hungarian and English translations included
- 📊 **Analytics**: Conversation tracking and statistics
- ⚙️ **Flexible Configuration**: Extensive config options
- 🔒 **Secure**: Rate limiting and content filtering

## Installation

Install the package via Composer:

```bash
composer require gothe92/filament-chatbot
```

Alternatively, you can install from the GitHub repository:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/gothe92/filament-chatbot.git"
        }
    ]
}
```

Then install:

```bash
composer require gothe92/filament-chatbot:dev-main
```

After installation, publish and run migrations:

```bash
php artisan vendor:publish --tag="filament-chatbot-migrations"
php artisan migrate
```

Publish config (optional):

```bash
php artisan vendor:publish --tag="filament-chatbot-config"
```

Publish assets:

```bash
php artisan vendor:publish --tag="filament-chatbot-assets"
```

## Quick Start

### 1. Add Trait to Your Model

```php
<?php

namespace App\Models;

use FilamentChatbot\Traits\HasChatbot;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasChatbot;
    
    protected $fillable = ['name', 'description'];
}
```

### 2. Add Relation Managers to Your Filament Resource

```php
<?php

namespace App\Filament\Resources;

use FilamentChatbot\Filament\Actions\AddChatbotRelationsAction;

class ProductResource extends Resource
{
    // ... your existing code ...
    
    public static function getRelations(): array
    {
        return [
            ...AddChatbotRelationsAction::addTo(self::class),
            // vagy csak specifikus relation-öket:
            // ...AddChatbotRelationsAction::only(['documents', 'questions']),
        ];
    }
}
```

### 3. Use in Blade Template

```blade
<!-- Basic usage with default styling -->
<livewire:filament-chatbot::chatbot-widget :model="$product" />

<!-- With custom view -->
<livewire:filament-chatbot::chatbot-widget 
    :model="$product" 
    :custom-view="'custom.chatbot-view'" />

<!-- With additional options -->
<livewire:filament-chatbot::chatbot-widget 
    :model="$product" 
    :show-predefined-questions="true" />
```

## Customization

### Custom Blade View

Create your custom view extending the skeleton:

```blade
<!-- resources/views/custom/chatbot-view.blade.php -->
<div class="my-custom-chatbot bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl shadow-xl" 
     x-data="chatbotComponent(@js($messages))"
     x-init="initChatbot()"
     wire:ignore.self>
    
    <!-- Custom Header -->
    <div class="p-6 text-white">
        <h2 class="text-xl font-bold">{{ $model->name }}</h2>
        <p class="text-sm opacity-90">Ask me anything!</p>
    </div>
    
    <!-- Messages -->
    <div class="messages-container h-96 overflow-y-auto p-4 bg-white/10 backdrop-blur" 
         x-ref="messagesContainer">
        <template x-for="message in messages" :key="message.created_at">
            <div x-html="renderMessage(message)" class="mb-4"></div>
        </template>
    </div>
    
    <!-- Custom Input -->
    <div class="p-4">
        <form wire:submit="sendMessage" class="flex gap-3">
            <input type="text"
                   wire:model="input"
                   placeholder="Type something magical..."
                   class="flex-1 rounded-full px-6 py-3 bg-white/20 text-white placeholder-white/70 border-0 focus:ring-2 focus:ring-white/50">
            <button type="submit" 
                    class="bg-white text-purple-600 rounded-full px-6 py-3 font-semibold hover:bg-gray-100">
                Send ✨
            </button>
        </form>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('vendor/filament-chatbot/js/chatbot.js') }}"></script>
@endpush
```

### Custom JavaScript Behavior

Override or extend the default JavaScript:

```javascript
// resources/js/custom-chatbot.js

// Extend the base component
window.chatbotComponent = function(initialMessages) {
    const baseComponent = window.chatbotComponentBase(initialMessages);
    
    return {
        ...baseComponent,
        
        // Override message rendering
        renderMessage(message) {
            const isUser = message.role === 'user';
            
            return `
                <div class="flex ${isUser ? 'justify-end' : 'justify-start'} mb-4">
                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-2xl ${
                        isUser 
                            ? 'bg-purple-500 text-white' 
                            : 'bg-white text-gray-800 shadow-lg'
                    }">
                        <div class="text-sm">${this.parseContent(message.content)}</div>
                        <div class="text-xs opacity-70 mt-1">
                            ${this.formatTime(message.created_at)}
                        </div>
                    </div>
                </div>
            `;
        },
        
        // Add custom notification sound
        onMessageReceived(message) {
            // Call parent method
            baseComponent.onMessageReceived.call(this, message);
            
            // Add custom logic
            this.playCustomSound();
            this.showCustomNotification(message);
        },
        
        playCustomSound() {
            // Your custom sound logic
            console.log('🔔 New message received!');
        },
        
        showCustomNotification(message) {
            // Custom notification
            if (Notification.permission === 'granted') {
                new Notification('New message', {
                    body: message.content.substring(0, 50) + '...',
                    icon: '/icon.png'
                });
            }
        }
    };
};
```

## Document Management

### Add Documents to Chatbot

```php
// Add a document
$product->addChatbotDocument(
    'Product Manual', 
    'This is the product manual content...',
    ['type' => 'manual', 'version' => '1.0']
);

// Or create directly through relationship
$product->chatbotDocuments()->create([
    'title' => 'FAQ Document',
    'content' => 'Frequently asked questions...',
    'metadata' => ['category' => 'support']
]);
```

### Predefined Questions

```php
// Add predefined questions
$product->addPredefinedQuestion(
    'What are the dimensions?', 
    'The product dimensions are 10x20x5 cm.',
    1 // order
);

$product->addPredefinedQuestion(
    'What materials is it made from?',
    'It is made from high-quality aluminum and plastic.',
    2
);
```

## RAG Modes

Configure how the chatbot generates responses:

```php
// Documents only - strict mode
$product->updateChatbotSettings(['rag_mode' => 'documents_only']);

// Documents + AI knowledge (default)
$product->updateChatbotSettings(['rag_mode' => 'documents_and_ai']);

// AI knowledge only
$product->updateChatbotSettings(['rag_mode' => 'ai_only']);

// All documents (expensive)
$product->updateChatbotSettings(['rag_mode' => 'all_documents']);
```

## API Usage

The plugin provides REST API endpoints:

```javascript
// Send a message
fetch('/api/chatbot/message', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        message: 'What is this product?',
        chatbot_resource_id: 1,
        session_id: 'unique-session-id'
    })
});

// Get conversation history
fetch('/api/chatbot/conversation/unique-session-id');

// Get predefined questions
fetch('/api/chatbot/predefined-questions/1');
```

## Configuration

Key configuration options in `config/filament-chatbot.php`:

```php
return [
    'defaults' => [
        'rag_mode' => 'documents_and_ai',
        'response_length' => 200,
        'temperature' => 0.7,
    ],
    
    'ui' => [
        'show_typing_indicator' => true,
        'show_predefined_questions' => true,
        'enable_sound_notifications' => true,
    ],
    
    'ai' => [
        'provider' => 'claude', // or 'openai'
        'max_tokens' => 1024,
    ],
    
    'security' => [
        'rate_limit' => [
            'max_messages_per_minute' => 20,
        ],
    ],
];
```

## Advanced Usage

### Multiple Chatbot Configurations

```php
// Create different chatbot settings for different models
$technicalProduct->enableChatbot([
    'rag_mode' => 'documents_only',
    'metadata' => ['expertise_level' => 'expert']
]);

$consumerProduct->enableChatbot([
    'rag_mode' => 'documents_and_ai', 
    'metadata' => ['expertise_level' => 'beginner']
]);
```

### Integration with Existing Services

```php
// The plugin automatically detects and uses your existing services
// - RAGService (for advanced document search)
// - AIServiceFactory (for Claude/OpenAI integration)
// - EmbeddingService (for document embeddings)

// If these services exist, they'll be used automatically
// Otherwise, the plugin falls back to basic implementations
```

### Filament Admin Integration

The plugin automatically provides admin resources when used with Filament:

- View and manage all chatbot resources
- Monitor conversations and analytics
- Manage documents and predefined questions
- Configure RAG modes and settings

## Events and Hooks

Listen to chatbot events:

```php
// In your EventServiceProvider
use FilamentChatbot\Events\MessageSent;
use FilamentChatbot\Events\MessageReceived;

protected $listen = [
    MessageSent::class => [
        YourMessageSentListener::class,
    ],
    MessageReceived::class => [
        YourMessageReceivedListener::class,
    ],
];
```

## Testing

The plugin includes comprehensive tests:

```bash
composer test
```

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support

For support, please open an issue on GitHub or contact the development team.