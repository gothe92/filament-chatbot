<?php

namespace FilamentChatbot\Filament\Resources\ChatbotSettingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use FilamentChatbot\Filament\Resources\ChatbotSettingResource;

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
