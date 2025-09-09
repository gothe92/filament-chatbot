<?php

namespace FilamentChatbot\Filament\Resources\ChatbotSettingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use FilamentChatbot\Filament\Resources\ChatbotSettingResource;

class EditChatbotSetting extends EditRecord
{
    protected static string $resource = ChatbotSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If this is set as default, remove default from others
        if ($data['is_default'] ?? false) {
            $this->getModel()::where('id', '!=', $this->getRecord()->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        return $data;
    }
}
