<?php

namespace App\Filament\Resources\EventCategories;

use App\Filament\Resources\EventCategories\Pages\CreateEventCategory;
use App\Filament\Resources\EventCategories\Pages\EditEventCategory;
use App\Filament\Resources\EventCategories\Pages\ListEventCategories;
use App\Filament\Resources\EventCategories\Schemas\EventCategoryForm;
use App\Filament\Resources\EventCategories\Tables\EventCategoriesTable;
use App\Models\EventCategory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EventCategoryResource extends Resource
{
    protected static ?string $model = EventCategory::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Events Management';

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return EventCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventCategoriesTable::configure($table);
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
            'index' => ListEventCategories::route('/'),
            'create' => CreateEventCategory::route('/create'),
            'edit' => EditEventCategory::route('/{record}/edit'),
        ];
    }
}
