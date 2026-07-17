<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Models\Faq;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string | UnitEnum | null $navigationGroup = 'Content';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $recordTitleAttribute = 'question';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('faq_category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        TextInput::make('slug')->required(),
                        TextInput::make('icon')->placeholder('e.g. bi-credit-card'),
                    ]),
                TextInput::make('question')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('answer')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('question')
                    ->searchable()
                    ->limit(60),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}

