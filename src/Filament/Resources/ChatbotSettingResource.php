<?php

namespace FilamentChatbot\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentChatbot\Filament\Resources\ChatbotSettingResource\Pages;
use FilamentChatbot\Models\ChatbotSetting;

class ChatbotSettingResource extends Resource
{
    protected static ?string $model = ChatbotSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Chatbot Settings';

    protected static ?string $navigationGroup = 'Chatbot';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (?string $state, Forms\Set $set) => $set('name', ucfirst($state))
                            ),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000),

                        Forms\Components\Toggle::make('is_default')
                            ->label('Default Setting')
                            ->helperText('Only one setting can be default at a time'),

                        Forms\Components\Toggle::make('active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Behavior Configuration')
                    ->schema([
                        Forms\Components\Select::make('personality')
                            ->options(ChatbotSetting::getPersonalities())
                            ->required()
                            ->default(ChatbotSetting::PERSONALITY_HELPFUL),

                        Forms\Components\Select::make('tone')
                            ->options(ChatbotSetting::getTones())
                            ->required()
                            ->default(ChatbotSetting::TONE_PROFESSIONAL),

                        Forms\Components\Select::make('expertise_level')
                            ->label('Expertise Level')
                            ->options(ChatbotSetting::getExpertiseLevels())
                            ->required()
                            ->default(ChatbotSetting::EXPERTISE_INTERMEDIATE),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Response Settings')
                    ->schema([
                        Forms\Components\TextInput::make('response_length')
                            ->label('Max Response Length (words)')
                            ->numeric()
                            ->default(200)
                            ->minValue(50)
                            ->maxValue(1000)
                            ->step(50),

                        Forms\Components\TextInput::make('temperature')
                            ->label('Temperature (creativity)')
                            ->numeric()
                            ->default(0.7)
                            ->minValue(0.1)
                            ->maxValue(1.0)
                            ->step(0.1)
                            ->helperText('Lower values = more focused, Higher values = more creative'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Content Restrictions')
                    ->schema([
                        Forms\Components\TagsInput::make('forbidden_topics')
                            ->label('Forbidden Topics')
                            ->placeholder('Add topics to avoid discussing...')
                            ->helperText('Topics the chatbot should avoid discussing'),

                        Forms\Components\TagsInput::make('avoided_expressions')
                            ->label('Avoided Expressions')
                            ->placeholder('Add expressions to avoid...')
                            ->helperText('Words or phrases the chatbot should avoid using'),
                    ]),

                Forms\Components\Section::make('Custom Instructions')
                    ->schema([
                        Forms\Components\Textarea::make('behavioral_rules')
                            ->label('Behavioral Rules')
                            ->rows(4)
                            ->placeholder('Define specific behavioral rules for this chatbot...')
                            ->helperText('Specific rules about how the chatbot should behave'),

                        Forms\Components\Textarea::make('custom_instructions')
                            ->label('Custom Instructions')
                            ->rows(4)
                            ->placeholder('Add any additional custom instructions...')
                            ->helperText('Additional instructions for the chatbot'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('personality')
                    ->badge()
                    ->formatStateUsing(fn (ChatbotSetting $record) => $record->getPersonalityLabel()),

                Tables\Columns\TextColumn::make('tone')
                    ->badge()
                    ->formatStateUsing(fn (ChatbotSetting $record) => $record->getToneLabel()),

                Tables\Columns\TextColumn::make('expertise_level')
                    ->label('Expertise')
                    ->badge()
                    ->formatStateUsing(fn (ChatbotSetting $record) => $record->getExpertiseLevelLabel()),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-outline-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('chatbot_resources_count')
                    ->label('Used By')
                    ->counts('chatbotResources')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('personality')
                    ->options(ChatbotSetting::getPersonalities()),

                Tables\Filters\SelectFilter::make('tone')
                    ->options(ChatbotSetting::getTones()),

                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Default Setting'),

                Tables\Filters\TernaryFilter::make('active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('set_default')
                    ->label('Set as Default')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn (ChatbotSetting $record) => ! $record->is_default)
                    ->requiresConfirmation()
                    ->action(fn (ChatbotSetting $record) => $record->setAsDefault())
                    ->successNotificationTitle('Default setting updated'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatbotSettings::route('/'),
            'create' => Pages\CreateChatbotSetting::route('/create'),
            'view' => Pages\ViewChatbotSetting::route('/{record}'),
            'edit' => Pages\EditChatbotSetting::route('/{record}/edit'),
        ];
    }
}
