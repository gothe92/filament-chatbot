/**
 * Filament Chatbot Component
 * Customizable JavaScript for chatbot functionality
 */

// Store base component for extension
window.chatbotComponentBase = function(initialMessages = []) {
    return {
        messages: initialMessages,
        isTyping: false,
        
        /**
         * Initialize chatbot
         */
        initChatbot() {
            console.log('Chatbot initialized');
            this.scrollToBottom();
            this.setupEventListeners();
            this.setupMarkdown();
        },
        
        /**
         * Setup markdown parser if available
         */
        setupMarkdown() {
            // Check if marked.js is available
            if (typeof marked !== 'undefined') {
                this.markdownAvailable = true;
                marked.setOptions({
                    breaks: true,
                    gfm: true,
                });
            } else {
                this.markdownAvailable = false;
            }
        },
        
        /**
         * Handle send button/enter key
         */
        handleSend() {
            if (!this.$wire.input.trim()) {
                return;
            }
            
            // Let Livewire handle the actual sending
            // This is just for any additional client-side logic
            this.beforeMessageSend();
        },
        
        /**
         * Render a message (can be overridden)
         */
        renderMessage(message) {
            const isUser = message.role === 'user';
            const isSystem = message.role === 'system';
            const time = message.created_at ? new Date(message.created_at).toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit' 
            }) : '';
            
            const messageClass = isUser ? 'user-message' : (isSystem ? 'system-message' : 'assistant-message');
            const bgClass = isUser ? 'bg-blue-600 text-white' : (isSystem ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800');
            const alignment = isUser ? 'justify-end' : 'justify-start';
            
            const content = this.parseContent(message.content);
            
            return `
                <div class="message-wrapper flex ${alignment} mb-3 message-${messageClass}">
                    <div class="message max-w-[70%]">
                        ${!isUser && !isSystem ? this.renderAssistantAvatar() : ''}
                        ${isUser ? this.renderUserAvatar() : ''}
                        <div class="message-content ${bgClass} rounded-lg px-4 py-2.5">
                            ${content}
                        </div>
                        ${time ? `<div class="text-xs text-gray-400 mt-1 ${isUser ? 'text-right' : 'text-left'}">${time}</div>` : ''}
                    </div>
                </div>
            `;
        },
        
        /**
         * Render assistant avatar
         */
        renderAssistantAvatar() {
            return `
                <div class="flex items-center space-x-2 mb-1">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500">Assistant</span>
                </div>
            `;
        },
        
        /**
         * Render user avatar
         */
        renderUserAvatar() {
            return `
                <div class="flex items-center space-x-2 mb-1 justify-end">
                    <span class="text-xs text-gray-500">You</span>
                    <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                            </path>
                        </svg>
                    </div>
                </div>
            `;
        },
        
        /**
         * Parse message content (markdown, links, etc.)
         */
        parseContent(text) {
            // Escape HTML first
            let content = this.escapeHtml(text);
            
            // Parse markdown if available
            if (this.markdownAvailable) {
                content = marked.parse(content);
            } else {
                // Basic formatting without markdown library
                content = this.basicFormatting(content);
            }
            
            // Auto-link URLs
            content = this.autoLink(content);
            
            return content;
        },
        
        /**
         * Basic text formatting without markdown
         */
        basicFormatting(text) {
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Bold
                .replace(/\*(.*?)\*/g, '<em>$1</em>') // Italic
                .replace(/`(.*?)`/g, '<code>$1</code>') // Inline code
                .replace(/\n/g, '<br>'); // Line breaks
        },
        
        /**
         * Auto-link URLs in text
         */
        autoLink(text) {
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            return text.replace(urlRegex, '<a href="$1" target="_blank" class="text-blue-500 underline">$1</a>');
        },
        
        /**
         * Escape HTML to prevent XSS
         */
        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        },
        
        /**
         * Scroll to bottom of messages
         */
        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.messagesContainer) {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                }
            });
        },
        
        /**
         * Setup event listeners
         */
        setupEventListeners() {
            // Listen for Livewire events
            Livewire.on('messageSent', (message) => {
                this.onMessageSent(message);
            });
            
            Livewire.on('messageReceived', (message) => {
                this.onMessageReceived(message);
            });
            
            Livewire.on('conversationCleared', () => {
                this.onConversationCleared();
            });
            
            // Listen for typing events
            this.$watch('$wire.isTyping', (value) => {
                if (value) {
                    this.onTypingStart();
                } else {
                    this.onTypingEnd();
                }
            });
        },
        
        /**
         * Called before message is sent (can be overridden)
         */
        beforeMessageSend() {
            console.log('Sending message...');
        },
        
        /**
         * Called when message is sent (can be overridden)
         */
        onMessageSent(message) {
            console.log('Message sent:', message);
            this.scrollToBottom();
        },
        
        /**
         * Called when message is received (can be overridden)
         */
        onMessageReceived(message) {
            console.log('Message received:', message);
            this.scrollToBottom();
            
            // Optional: Play notification sound
            this.playNotificationSound();
        },
        
        /**
         * Called when conversation is cleared (can be overridden)
         */
        onConversationCleared() {
            console.log('Conversation cleared');
            this.messages = [];
        },
        
        /**
         * Called when typing starts (can be overridden)
         */
        onTypingStart() {
            console.log('Assistant is typing...');
            this.scrollToBottom();
        },
        
        /**
         * Called when typing ends (can be overridden)
         */
        onTypingEnd() {
            console.log('Assistant stopped typing');
        },
        
        /**
         * Play notification sound (optional)
         */
        playNotificationSound() {
            // Only play if user has interacted with the page
            if (this.soundEnabled && typeof Audio !== 'undefined') {
                try {
                    const audio = new Audio('/vendor/filament-chatbot/sounds/notification.mp3');
                    audio.volume = 0.3;
                    audio.play().catch(e => console.log('Could not play sound:', e));
                } catch (e) {
                    console.log('Audio not supported');
                }
            }
        },
        
        /**
         * Enable/disable sound
         */
        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            localStorage.setItem('chatbot_sound_enabled', this.soundEnabled);
        },
        
        /**
         * Additional helper methods
         */
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        },
        
        formatTime(dateString) {
            return new Date(dateString).toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },
        
        /**
         * Get message count
         */
        get messageCount() {
            return this.messages.length;
        },
        
        /**
         * Check if conversation is empty
         */
        get isConversationEmpty() {
            return this.messages.length === 0;
        },
        
        /**
         * Data properties
         */
        soundEnabled: localStorage.getItem('chatbot_sound_enabled') !== 'false',
        markdownAvailable: false,
    };
};

// Default component (can be overridden)
window.chatbotComponent = window.chatbotComponentBase;

// Optional: Custom extensions example
window.extendChatbot = function(customMethods = {}) {
    return function(initialMessages) {
        const baseComponent = window.chatbotComponentBase(initialMessages);
        return {
            ...baseComponent,
            ...customMethods
        };
    };
};