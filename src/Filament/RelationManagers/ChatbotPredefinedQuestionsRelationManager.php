<?php

namespace FilamentChatbot\Filament\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentChatbot\Models\ChatbotPredefinedQuestion;

class ChatbotPredefinedQuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'chatbotPredefinedQuestions';
    protected static ?string $recordTitleAttribute = 'question';
    protected static ?string $title = 'Predefined Questions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question')
                    ->required()
                    ->maxLength(500)
                    ->label(__('filament-chatbot::messages.admin.question')),
                
                Forms\Components\Textarea::make('answer')
                    ->required()
                    ->rows(4)
                    ->label(__('filament-chatbot::messages.admin.answer')),
                
                Forms\Components\Toggle::make('active')
                    ->default(true)
                    ->label(__('filament-chatbot::messages.admin.active')),
                
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->label(__('filament-chatbot::messages.admin.order')),
                
                Forms\Components\KeyValue::make('metadata')
                    ->label(__('filament-chatbot::messages.admin.metadata'))
                    ->keyLabel('Key')
                    ->valueLabel('Value'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable()
                    ->width(60)
                    ->label('#'),
                
                Tables\Columns\TextColumn::make('question')
                    ->searchable()
                    ->limit(60)
                    ->label(__('filament-chatbot::messages.admin.question')),
                
                Tables\Columns\TextColumn::make('answer')
                    ->limit(80)
                    ->label(__('filament-chatbot::messages.admin.answer')),
                
                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->label(__('filament-chatbot::messages.admin.active')),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label(__('filament-chatbot::messages.admin.active')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('filament-chatbot::messages.admin.add_question'))
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
                
                Tables\Actions\Action::make('move_up')
                    ->icon('heroicon-o-arrow-up')
                    ->label('Move Up')
                    ->action(function (ChatbotPredefinedQuestion $record) {
                        $record->moveUp();
                    })
                    ->visible(fn (ChatbotPredefinedQuestion $record) => $record->order > 0),
                
                Tables\Actions\Action::make('move_down')
                    ->icon('heroicon-o-arrow-down')
                    ->label('Move Down')
                    ->action(function (ChatbotPredefinedQuestion $record) {
                        $record->moveDown();
                    }),
                
                Tables\Actions\Action::make('toggle_active')
                    ->icon(fn (ChatbotPredefinedQuestion $record) => $record->active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->label(fn (ChatbotPredefinedQuestion $record) => $record->active ? 'Deactivate' : 'Activate')
                    ->action(function (ChatbotPredefinedQuestion $record) {
                        $record->toggleActive();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records) {
                            $records->each->update(['active' => true]);
                        }),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-eye-slash')
                        ->action(function ($records) {
                            $records->each->update(['active' => false]);
                        }),
                ]),
            ])
            ->defaultSort('order')
            ->emptyStateHeading(__('filament-chatbot::messages.admin.predefined_questions'))
            ->emptyStateDescription(__('filament-chatbot::messages.admin.predefined_questions_description'))
            ->emptyStateIcon('heroicon-o-question-mark-circle');
    }
}