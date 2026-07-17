<?php

namespace App\Filament\Resources\BusinessCategories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BusinessCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                TextInput::make('slug')
                    ->required(),
                Select::make('type')
                    ->options([
                        'contractor' => 'Contractor',
                        'vendor' => 'Vendor',
                    ])
                    ->required()
                    ->default('contractor')
                    ->live(),
                Select::make('category_type')
                    ->options([
                        'parent' => 'Parent Category',
                        'child' => 'Sub-Category (Child)',
                    ])
                    ->required()
                    ->default('child')
                    ->live(),
                Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'name', fn ($query, $get) => 
                        $query->where('type', $get('type'))->where('category_type', 'parent')
                    )
                    ->visible(fn ($get) => $get('category_type') === 'child')
                    ->nullable()
                    ->searchable()
                    ->preload(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->image()
                    ->directory('business-categories'),
            ]);
    }
}
