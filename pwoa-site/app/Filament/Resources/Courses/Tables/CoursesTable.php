<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_path')
                    ->label('Thumbnail')
                    ->circular(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => Str::limit($record->description, 50)),
                TextColumn::make('lessons_count')
                    ->counts('lessons')
                    ->label('Lessons')
                    ->badge()
                    ->color('info'),
                TextColumn::make('quiz_count')
                    ->counts('quiz')
                    ->label('Quizzes')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Enrolled')
                    ->badge()
                    ->color('success'),
                TextColumn::make('quiz_results_count')
                    ->counts('quizResults')
                    ->label('Quiz Attempts')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('certificates_count')
                    ->counts('certificates')
                    ->label('Certified')
                    ->badge()
                    ->color('primary'),
                ToggleColumn::make('is_published')
                    ->label('Published'),
                TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
