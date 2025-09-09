<?php

namespace FilamentChatbot\Filament\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentChatbot\Models\ChatbotDocument;

class ChatbotDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'chatbotDocuments';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $title = 'Chatbot Documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label(__('filament-chatbot::messages.admin.title')),

                Forms\Components\Textarea::make('content')
                    ->required()
                    ->rows(6)
                    ->label(__('filament-chatbot::messages.admin.content')),

                Forms\Components\FileUpload::make('file_path')
                    ->label(__('filament-chatbot::messages.admin.file_path'))
                    ->acceptedFileTypes(['application/pdf', 'text/plain', 'application/msword'])
                    ->directory('chatbot-documents')
                    ->maxSize(10240),

                Forms\Components\KeyValue::make('metadata')
                    ->label(__('filament-chatbot::messages.admin.metadata'))
                    ->keyLabel('Key')
                    ->valueLabel('Value'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label(__('filament-chatbot::messages.admin.title')),

                Tables\Columns\TextColumn::make('file_type')
                    ->badge()
                    ->label(__('filament-chatbot::messages.admin.file_type')),

                Tables\Columns\TextColumn::make('chunks_count')
                    ->numeric()
                    ->sortable()
                    ->label(__('filament-chatbot::messages.admin.chunks_count')),

                Tables\Columns\TextColumn::make('tokens_count')
                    ->numeric()
                    ->sortable()
                    ->label('Tokens'),

                Tables\Columns\IconColumn::make('has_embedding')
                    ->boolean()
                    ->getStateUsing(fn (ChatbotDocument $record) => ! empty($record->embedding))
                    ->label(__('filament-chatbot::messages.admin.embedding')),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('file_type')
                    ->options([
                        'pdf' => 'PDF',
                        'txt' => 'Text',
                        'doc' => 'Word',
                        'docx' => 'Word',
                    ])
                    ->label(__('filament-chatbot::messages.admin.file_type')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('filament-chatbot::messages.admin.upload_document'))
                    ->mutateFormDataUsing(function (array $data): array {
                        // Auto-create chatbot resource if needed
                        $chatbotResource = $this->getOwnerRecord()->getOrCreateChatbotResource();
                        $data['chatbot_resource_id'] = $chatbotResource->id;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make('generate_embeddings')
                    ->icon('heroicon-o-cpu-chip')
                    ->label(__('filament-chatbot::messages.admin.generate_embeddings'))
                    ->action(function (ChatbotDocument $record) {
                        // Dispatch job to generate embeddings
                        if (class_exists('\App\Jobs\ProcessDocument')) {
                            \App\Jobs\ProcessDocument::dispatch($record);
                        }

                        $this->notify('success', __('filament-chatbot::messages.success.embeddings_generated'));
                    })
                    ->requiresConfirmation()
                    ->visible(fn (ChatbotDocument $record) => empty($record->embedding)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('filament-chatbot::messages.admin.documents'))
            ->emptyStateDescription(__('filament-chatbot::messages.admin.documents_description'))
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
