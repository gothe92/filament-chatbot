<?php

namespace FilamentChatbot\Filament\Resources\ChatbotSettingResource\Pages;

use FilamentChatbot\Filament\Resources\ChatbotSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChatbotSetting extends CreateRecord
{
    protected static string $resource = ChatbotSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If this is set as default, remove default from others
        if ($data['is_default'] ?? false) {
            $this->getModel()::where('is_default', true)->update(['is_default' => false]);
        }

        return $data;
    }
}