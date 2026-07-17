<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Course Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->weight('bold')
                                    ->size('lg'),
                                TextEntry::make('slug')
                                    ->color('gray'),
                            ]),
                        TextEntry::make('description')
                            ->markdown()
                            ->columnSpanFull(),
                    ])->columnSpan(2),

                Section::make('Status & Metadata')
                    ->schema([
                        ImageEntry::make('thumbnail_path')
                            ->label('Thumbnail')
                            ->circular(),
                        IconEntry::make('is_published')
                            ->label('Published Status')
                            ->boolean(),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Created On'),
                    ])->columnSpan(1),
            ])
            ->columns(3);
    }
}
