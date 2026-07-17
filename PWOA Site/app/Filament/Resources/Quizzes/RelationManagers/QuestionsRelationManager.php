<?php

namespace App\Filament\Resources\Quizzes\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $recordTitleAttribute = 'question_text';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('question_text')
                    ->required()
                    ->columnSpanFull(),

                Repeater::make('options')
                    ->relationship()
                    ->schema([
                        TextInput::make('text')
                            ->required()
                            ->label('Option Text'),
                    ])
                    ->grid(2)
                    ->label('Answer Options')
                    ->required()
                    ->minItems(2),

                TextInput::make('correct_answer')
                    ->helperText('Exact text of the correct option above.')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question_text')
                    ->limit(50),
                TextColumn::make('correct_answer')
                    ->color('success'),
                TextColumn::make('options_count')
                    ->counts('options')
                    ->label('Options'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
