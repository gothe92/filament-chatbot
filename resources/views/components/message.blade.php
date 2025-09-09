@php
    $isUser = $message['role'] === 'user';
    $isSystem = $message['role'] === 'system';
@endphp

<div class="message-wrapper flex {{ $isUser ? 'justify-end' : 'justify-start' }} mb-3">
    <div class="message max-w-[70%] {{ $isUser ? 'order-2' : 'order-1' }}">
        @if(!$isUser && !$isSystem)
            <div class="flex items-center space-x-2 mb-1">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <span class="text-xs text-gray-500">{{ __('filament-chatbot::messages.assistant') }}</span>
            </div>
        @endif
        
        @if($isUser)
            <div class="flex items-center space-x-2 mb-1 justify-end">
                <span class="text-xs text-gray-500">{{ __('filament-chatbot::messages.you') }}</span>
                <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                        </path>
                    </svg>
                </div>
            </div>
        @endif
        
        <div class="message-content {{ $isUser ? 'bg-blue-600 text-white' : ($isSystem ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-gray-100 text-gray-800') }} rounded-lg px-4 py-2.5">
            @if($isSystem)
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <div class="prose prose-sm max-w-none">
                        {!! nl2br(e($message['content'])) !!}
                    </div>
                </div>
            @else
                <div class="prose prose-sm max-w-none {{ $isUser ? 'prose-invert' : '' }}">
                    {!! nl2br(e($message['content'])) !!}
                </div>
            @endif
        </div>
        
        @if(isset($message['created_at']))
            <div class="text-xs text-gray-400 mt-1 {{ $isUser ? 'text-right' : 'text-left' }}">
                {{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}
            </div>
        @endif
    </div>
</div>