<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuizRelationManager extends RelationManager
{
    protected static string $relationship = 'quiz';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('pass_percentage')
                    ->numeric()
                    ->default(80)
                    ->suffix('%'),
                Toggle::make('is_published')
                    ->default(false),

                Repeater::make('questions')
                    ->relationship()
                    ->schema([
                        Textarea::make('question_text')
                            ->required()
                            ->columnSpanFull(),
                        Repeater::make('options')
                            ->schema([
                                TextInput::make('text')->required(),
                            ])
                            ->grid(2)
                            ->label('Options')
                            ->minItems(2)
                            ->required(),
                        TextInput::make('correct_answer')
                            ->helperText('Type the exact text of the correct option')
                            ->required(),
                    ])
                    ->columnSpanFull()
                    ->collapsed()
                    ->minItems(1)
                    ->itemLabel(fn(array $state): ?string => $state['question_text'] ?? null),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('pass_percentage')
                    ->suffix('%'),
                IconColumn::make('is_published')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->hidden(fn (\Filament\Resources\RelationManagers\RelationManager $livewire) => $livewire->getOwnerRecord()->quiz()->exists()),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
                DeleteAction::make(),
            ]);
    }
}
