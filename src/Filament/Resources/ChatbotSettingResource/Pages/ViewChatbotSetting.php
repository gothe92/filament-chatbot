<?php

namespace FilamentChatbot\Filament\Resources\ChatbotSettingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use FilamentChatbot\Filament\Resources\ChatbotSettingResource;

class ViewChatbotSetting extends ViewRecord
{
    protected static string $resource = ChatbotSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
