<?php

namespace FilamentChatbot\Filament\Resources\ChatbotSettingResource\Pages;

use FilamentChatbot\Filament\Resources\ChatbotSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatbotSettings extends ListRecords
{
    protected static string $resource = ChatbotSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}