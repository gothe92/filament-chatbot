{{-- Minimalist skeleton template for custom styling --}}
<div class="chatbot-custom" 
     x-data="chatbotComponent(@js($messages))"
     x-init="initChatbot()"
     wire:ignore.self>
    
    {{-- Messages area - customize as needed --}}
    <div class="messages-area" x-ref="messagesContainer">
        <template x-for="message in messages" :key="message.created_at">
            <div x-html="renderMessage(message)"></div>
        </template>
        
        {{-- Typing indicator --}}
        <div x-show="$wire.isTyping" x-transition>
            <span>{{ __('filament-chatbot::messages.typing') }}</span>
        </div>
    </div>
    
    {{-- Input area - customize as needed --}}
    <div class="input-area">
        <form wire:submit="sendMessage">
            <input type="text"
                   wire:model="input" 
                   x-on:keydown.enter="handleSend"
                   placeholder="{{ __('filament-chatbot::messages.type_message') }}">
            <button type="submit">
                {{ __('filament-chatbot::messages.send') }}
            </button>
        </form>
    </div>
    
    {{-- Optional: Predefined questions --}}
    @if($showPredefinedQuestions && count($predefinedQuestions) > 0)
        <div class="questions-area">
            @foreach($predefinedQuestions as $question)
                <button wire:click="askQuestion('{{ $question['question'] }}')">
                    {{ $question['question'] }}
                </button>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
    <script src="{{ asset('vendor/filament-chatbot/js/chatbot.js') }}"></script>
@endpush