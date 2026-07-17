<?php

namespace App\Filament\Resources\Quizzes;

use App\Filament\Resources\Quizzes\Pages\CreateQuiz;
use App\Filament\Resources\Quizzes\Pages\EditQuiz;
use App\Filament\Resources\Quizzes\Pages\ListQuizzes;
use App\Models\Quiz;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    protected static string | UnitEnum | null $navigationGroup = 'Learning Center';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $recordTitleAttribute = 'title';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('pass_percentage')
                    ->numeric()
                    ->default(80)
                    ->suffix('%'),
                Toggle::make('is_published')
                    ->default(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('pass_percentage')
                    ->suffix('%')
                    ->sortable(),
                IconColumn::make('is_published')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Questions'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuizzes::route('/'),
            'create' => CreateQuiz::route('/create'),
            'edit' => EditQuiz::route('/{record}/edit'),
        ];
    }
}
