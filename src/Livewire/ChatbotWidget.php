<?php

namespace FilamentChatbot\Livewire;

use FilamentChatbot\Models\ChatbotConversation;
use FilamentChatbot\Models\ChatbotMessage;
use FilamentChatbot\Services\ChatbotService;
use Illuminate\Support\Str;
use Livewire\Component;

class ChatbotWidget extends Component
{
    public $model;

    public $modelType;

    public $modelId;

    public $messages = [];

    public $input = '';

    public $isTyping = false;

    public $sessionId;

    public $viewName = 'filament-chatbot::livewire.chatbot-widget';

    public $customView = null;

    public $predefinedQuestions = [];

    public $showPredefinedQuestions = true;

    public $conversationId = null;

    public $chatbotResource = null;

    protected $listeners = ['messageReceived'];

    public function mount($model, $customView = null, $showPredefinedQuestions = true)
    {
        $this->model = $model;
        $this->modelType = get_class($model);
        $this->modelId = $model->id;
        $this->sessionId = session()->getId() ?: Str::uuid()->toString();
        $this->showPredefinedQuestions = $showPredefinedQuestions;

        // Custom view override
        if ($customView) {
            $this->customView = $customView;
            $this->viewName = $customView;
        }

        // Check if model has chatbot
        if (! method_exists($model, 'hasChatbot') || ! $model->hasChatbot()) {
            $this->messages[] = [
                'role' => 'system',
                'content' => __('filament-chatbot::messages.chatbot_not_available'),
                'created_at' => now()->toISOString(),
            ];

            return;
        }

        $this->loadChatbotResource();
        $this->loadConversationHistory();

        if ($this->showPredefinedQuestions) {
            $this->loadPredefinedQuestions();
        }
    }

    protected function loadChatbotResource()
    {
        $this->chatbotResource = $this->model->chatbotResource;

        if (! $this->chatbotResource || ! $this->chatbotResource->active) {
            $this->messages[] = [
                'role' => 'system',
                'content' => __('filament-chatbot::messages.chatbot_disabled'),
                'created_at' => now()->toISOString(),
            ];

            return;
        }
    }

    protected function loadConversationHistory()
    {
        if (! $this->chatbotResource) {
            return;
        }

        // Find or create conversation
        $conversation = ChatbotConversation::firstOrCreate([
            'chatbot_resource_id' => $this->chatbotResource->id,
            'session_id' => $this->sessionId,
        ], [
            'language' => app()->getLocale(),
        ]);

        $this->conversationId = $conversation->id;

        // Load existing messages
        $messages = $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at', 'asc')
            ->get();

        $this->messages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'role' => $message->role,
                'content' => $message->content,
                'created_at' => $message->created_at->toISOString(),
            ];
        })->toArray();
    }

    protected function loadPredefinedQuestions()
    {
        if (! $this->chatbotResource) {
            return;
        }

        $this->predefinedQuestions = $this->chatbotResource
            ->predefinedQuestions()
            ->where('active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                ];
            })
            ->toArray();
    }

    public function sendMessage()
    {
        if (empty(trim($this->input)) || ! $this->chatbotResource) {
            return;
        }

        // Add user message
        $userMessage = [
            'role' => 'user',
            'content' => $this->input,
            'created_at' => now()->toISOString(),
        ];

        $this->messages[] = $userMessage;

        // Save user message to database
        $savedUserMessage = ChatbotMessage::create([
            'conversation_id' => $this->conversationId,
            'role' => 'user',
            'content' => $this->input,
            'tokens_used' => 0,
        ]);

        // Clear input and show typing indicator
        $message = $this->input;
        $this->input = '';
        $this->isTyping = true;

        // Emit event for JS handling
        $this->dispatch('messageSent', $userMessage);

        // Get response from chatbot service
        try {
            $chatbotService = app(ChatbotService::class);
            $response = $chatbotService->generateResponse(
                $message,
                $this->chatbotResource,
                $this->conversationId,
                app()->getLocale()
            );

            if ($response['success']) {
                $assistantMessage = [
                    'role' => 'assistant',
                    'content' => $response['content'],
                    'created_at' => now()->toISOString(),
                ];

                $this->messages[] = $assistantMessage;

                // Save assistant message
                ChatbotMessage::create([
                    'conversation_id' => $this->conversationId,
                    'role' => 'assistant',
                    'content' => $response['content'],
                    'tokens_used' => $response['tokens_used'] ?? 0,
                    'metadata' => [
                        'used_rag' => $response['used_rag'] ?? false,
                        'rag_documents' => $response['rag_documents'] ?? [],
                    ],
                ]);

                // Emit event for JS handling
                $this->dispatch('messageReceived', $assistantMessage);
            } else {
                $this->addErrorMessage();
            }
        } catch (\Exception $e) {
            \Log::error('Chatbot error: '.$e->getMessage());
            $this->addErrorMessage();
        }

        $this->isTyping = false;
    }

    public function askQuestion($question)
    {
        $this->input = $question;
        $this->sendMessage();
    }

    protected function addErrorMessage()
    {
        $errorMessage = [
            'role' => 'assistant',
            'content' => __('filament-chatbot::messages.error_generating_response'),
            'created_at' => now()->toISOString(),
        ];

        $this->messages[] = $errorMessage;
        $this->dispatch('messageReceived', $errorMessage);
    }

    public function clearConversation()
    {
        $this->messages = [];
        $this->sessionId = Str::uuid()->toString();
        $this->loadConversationHistory();

        $this->dispatch('conversationCleared');
    }

    public function render()
    {
        return view($this->viewName);
    }
}
