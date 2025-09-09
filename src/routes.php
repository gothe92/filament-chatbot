<?php

use FilamentChatbot\Http\Controllers\ChatbotApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/chatbot')->middleware(['api'])->group(function () {
    Route::post('/message', [ChatbotApiController::class, 'sendMessage']);
    Route::get('/conversations/{chatbotResourceId}', [ChatbotApiController::class, 'getConversations']);
    Route::get('/conversation/{sessionId}', [ChatbotApiController::class, 'getConversationHistory']);
    Route::delete('/conversation/{sessionId}', [ChatbotApiController::class, 'clearConversation']);
    Route::get('/predefined-questions/{chatbotResourceId}', [ChatbotApiController::class, 'getPredefinedQuestions']);
});
