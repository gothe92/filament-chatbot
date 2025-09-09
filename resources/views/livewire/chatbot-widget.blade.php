<div class="chatbot-container bg-white rounded-lg shadow-lg flex flex-col h-[600px] max-h-[80vh]" 
     x-data="chatbotComponent(@js($messages))"
     x-init="initChatbot()"
     wire:ignore.self>
    
    <!-- Header -->
    <div class="chatbot-header bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-t-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">{{ $model->name ?? __('filament-chatbot::messages.chatbot') }}</h3>
                    <p class="text-xs text-white/80">{{ __('filament-chatbot::messages.online') }}</p>
                </div>
            </div>
            <button @click="$wire.clearConversation()" 
                    class="text-white/80 hover:text-white transition-colors"
                    title="{{ __('filament-chatbot::messages.clear_conversation') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Messages -->
    <div class="chatbot-messages flex-1 overflow-y-auto p-4 space-y-3" x-ref="messagesContainer">
        @forelse($messages as $message)
            @include('filament-chatbot::components.message', ['message' => $message])
        @empty
            <div class="text-center text-gray-500 py-8">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                    </path>
                </svg>
                <p class="text-lg font-medium">{{ __('filament-chatbot::messages.start_conversation') }}</p>
                <p class="text-sm mt-1">{{ __('filament-chatbot::messages.ask_anything') }}</p>
            </div>
        @endforelse
        
        <!-- Typing indicator -->
        <div x-show="$wire.isTyping" 
             x-transition
             class="flex items-center space-x-2">
            <div class="bg-gray-200 rounded-lg px-4 py-3">
                <div class="typing-indicator flex space-x-1">
                    <span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Predefined Questions -->
    @if($showPredefinedQuestions && count($predefinedQuestions) > 0)
        <div class="predefined-questions border-t p-3 bg-gray-50">
            <p class="text-xs text-gray-600 mb-2">{{ __('filament-chatbot::messages.suggested_questions') }}:</p>
            <div class="flex flex-wrap gap-2">
                @foreach($predefinedQuestions as $question)
                    <button wire:click="askQuestion('{{ $question['question'] }}')"
                            class="question-chip bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-full text-sm hover:bg-blue-50 hover:border-blue-300 transition-colors">
                        {{ $question['question'] }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Input -->
    <div class="chatbot-input border-t p-4 bg-gray-50 rounded-b-lg">
        <form wire:submit="sendMessage" class="flex space-x-2">
            <input type="text"
                   wire:model="input"
                   x-on:keydown.enter="handleSend"
                   placeholder="{{ __('filament-chatbot::messages.type_message') }}"
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   :disabled="$wire.isTyping">
            
            <button type="submit"
                    :disabled="$wire.isTyping || !$wire.input.trim()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8">
                    </path>
                </svg>
            </button>
        </form>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('vendor/filament-chatbot/js/chatbot.js') }}"></script>
@endpush

<style>
    .typing-indicator span {
        animation: bounce 1.4s infinite ease-in-out both;
    }
    
    .typing-indicator span:nth-child(1) {
        animation-delay: -0.32s;
    }
    
    .typing-indicator span:nth-child(2) {
        animation-delay: -0.16s;
    }
    
    @keyframes bounce {
        0%, 80%, 100% {
            transform: scale(0);
            opacity: 0.5;
        }
        40% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>