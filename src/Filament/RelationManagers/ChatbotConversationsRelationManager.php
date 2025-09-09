<?php

namespace FilamentChatbot\Filament\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentChatbot\Models\ChatbotConversation;

class ChatbotConversationsRelationManager extends RelationManager
{
    protected static string $relationship = 'chatbotConversations';
    protected static ?string $recordTitleAttribute = 'session_id';
    protected static ?string $title = 'Chatbot Conversations';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('session_id')
                    ->disabled()
                    ->label(__('filament-chatbot::messages.admin.session_id')),
                
                Forms\Components\TextInput::make('language')
                    ->disabled()
                    ->label(__('filament-chatbot::messages.admin.language')),
                
                Forms\Components\KeyValue::make('metadata')
                    ->label(__('filament-chatbot::messages.admin.metadata'))
                    ->keyLabel('Key')
                    ->valueLabel('Value'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('session_id')
            ->columns([
                Tables\Columns\TextColumn::make('session_id')
                    ->searchable()
                    ->limit(20)
                    ->label(__('filament-chatbot::messages.admin.session_id')),
                
                Tables\Columns\TextColumn::make('language')
                    ->badge()
                    ->label(__('filament-chatbot::messages.admin.language')),
                
                Tables\Columns\TextColumn::make('messages_count')
                    ->getStateUsing(fn (ChatbotConversation $record) => $record->messages()->count())
                    ->numeric()
                    ->label(__('filament-chatbot::messages.admin.messages')),
                
                Tables\Columns\TextColumn::make('total_tokens')
                    ->getStateUsing(fn (ChatbotConversation $record) => $record->getTotalTokensUsed())
                    ->numeric()
                    ->label(__('filament-chatbot::messages.admin.tokens_used')),
                
                Tables\Columns\TextColumn::make('duration')
                    ->getStateUsing(fn (ChatbotConversation $record) => $record->getDurationInMinutes() . ' min')
                    ->label('Duration'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Started At'),
                
                Tables\Columns\TextColumn::make('last_message_at')
                    ->getStateUsing(function (ChatbotConversation $record) {
                        $lastMessage = $record->messages()->latest()->first();
                        return $lastMessage?->created_at?->diffForHumans();
                    })
                    ->label('Last Activity'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('language')
                    ->options([
                        'hu' => 'Hungarian',
                        'en' => 'English',
                    ])
                    ->label(__('filament-chatbot::messages.admin.language')),
                
                Tables\Filters\Filter::make('active_today')
                    ->query(fn ($query) => $query->whereHas('messages', function ($q) {
                        $q->whereDate('created_at', today());
                    }))
                    ->label('Active Today'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (ChatbotConversation $record) => 
                        route('filament.admin.resources.chatbot-conversations.view', $record)
                    )
                    ->openUrlInNewTab(),
                
                Tables\Actions\Action::make('view_messages')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->label(__('filament-chatbot::messages.admin.view_messages'))
                    ->modalContent(function (ChatbotConversation $record) {
                        $messages = $record->messages()->orderBy('created_at')->get();
                        
                        $content = '<div class="space-y-4">';
                        foreach ($messages as $message) {
                            $isUser = $message->role === 'user';
                            $bgColor = $isUser ? 'bg-blue-50' : 'bg-gray-50';
                            $content .= "
                                <div class='{$bgColor} p-3 rounded'>
                                    <div class='font-semibold text-sm'>{$message->role} - {$message->created_at->format('H:i:s')}</div>
                                    <div class='mt-1'>" . nl2br(e($message->content)) . "</div>
                                    <div class='text-xs text-gray-500 mt-1'>Tokens: {$message->tokens_used}</div>
                                </div>
                            ";
                        }
                        $content .= '</div>';
                        
                        return view('filament::components.modal.content', [
                            'content' => $content,
                        ]);
                    })
                    ->modalWidth('4xl'),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('filament-chatbot::messages.admin.conversations'))
            ->emptyStateDescription(__('filament-chatbot::messages.admin.conversation_description'))
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
    }
}