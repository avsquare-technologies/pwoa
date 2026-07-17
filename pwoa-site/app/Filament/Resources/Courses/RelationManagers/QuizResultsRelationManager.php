<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuizResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'quizResults';

    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->user->email),
                TextColumn::make('score')
                    ->suffix('%')
                    ->sortable()
                    ->alignment('center'),
                IconColumn::make('passed')
                    ->boolean()
                    ->label('Passed'),
                TextColumn::make('created_at')
                    ->label('Attempted At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
    }
}
