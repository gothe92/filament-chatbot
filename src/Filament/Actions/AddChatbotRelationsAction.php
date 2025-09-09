<?php

namespace FilamentChatbot\Filament\Actions;

use Filament\Resources\Resource;
use FilamentChatbot\Filament\RelationManagers\ChatbotDocumentsRelationManager;
use FilamentChatbot\Filament\RelationManagers\ChatbotPredefinedQuestionsRelationManager;
use FilamentChatbot\Filament\RelationManagers\ChatbotConversationsRelationManager;

class AddChatbotRelationsAction
{
    /**
     * Add chatbot relation managers to a Resource
     */
    public static function addTo(string $resourceClass): array
    {
        $relations = [];
        
        // Add documents relation manager
        $relations[] = ChatbotDocumentsRelationManager::class;
        
        // Add predefined questions relation manager
        $relations[] = ChatbotPredefinedQuestionsRelationManager::class;
        
        // Add conversations relation manager
        $relations[] = ChatbotConversationsRelationManager::class;
        
        return $relations;
    }
    
    /**
     * Get available chatbot relation managers
     */
    public static function getAvailableRelations(): array
    {
        return [
            'documents' => ChatbotDocumentsRelationManager::class,
            'questions' => ChatbotPredefinedQuestionsRelationManager::class,
            'conversations' => ChatbotConversationsRelationManager::class,
        ];
    }
    
    /**
     * Add specific chatbot relations
     */
    public static function only(array $relations): array
    {
        $available = self::getAvailableRelations();
        $result = [];
        
        foreach ($relations as $relation) {
            if (isset($available[$relation])) {
                $result[] = $available[$relation];
            }
        }
        
        return $result;
    }
}