<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->description('Basic course identifying details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                \Filament\Forms\Components\Select::make('course_category_id')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->label('Category')
                                    ->columnSpanFull(),
                            ]),
                        Textarea::make('description')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])->columnSpan(2),

                Section::make('Visuals & Status')
                    ->schema([
                        FileUpload::make('thumbnail_path')
                            ->image()
                            ->directory('course-thumbnails')
                            ->label('Course Thumbnail'),
                        Toggle::make('is_published')
                            ->label('Visible to Marketplace')
                            ->required(),
                    ])->columnSpan(1),
            ])
            ->columns(3);
    }
}
