<?php

use Illuminate\Support\Facades\Route;
use FilamentChatbot\Http\Controllers\ChatbotApiController;

Route::prefix('api/chatbot')->middleware(['api'])->group(function () {
    Route::post('/message', [ChatbotApiController::class, 'sendMessage']);
    Route::get('/conversations/{chatbotResourceId}', [ChatbotApiController::class, 'getConversations']);
    Route::get('/conversation/{sessionId}', [ChatbotApiController::class, 'getConversationHistory']);
    Route::delete('/conversation/{sessionId}', [ChatbotApiController::class, 'clearConversation']);
    Route::get('/predefined-questions/{chatbotResourceId}', [ChatbotApiController::class, 'getPredefinedQuestions']);
});