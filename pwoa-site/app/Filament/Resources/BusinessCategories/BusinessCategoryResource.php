<?php

namespace App\Filament\Resources\BusinessCategories;

use App\Filament\Resources\BusinessCategories\Pages\CreateBusinessCategory;
use App\Filament\Resources\BusinessCategories\Pages\EditBusinessCategory;
use App\Filament\Resources\BusinessCategories\Pages\ListBusinessCategories;
use App\Filament\Resources\BusinessCategories\Schemas\BusinessCategoryForm;
use App\Filament\Resources\BusinessCategories\Tables\BusinessCategoriesTable;
use App\Models\BusinessCategory;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BusinessCategoryResource extends Resource
{
    protected static ?string $model = BusinessCategory::class;

    protected static string | UnitEnum | null $navigationGroup = 'Business Directory';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BusinessCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusinessCategoriesTable::configure($table);
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
            'index' => ListBusinessCategories::route('/'),
            'create' => CreateBusinessCategory::route('/create'),
            'edit' => EditBusinessCategory::route('/{record}/edit'),
        ];
    }
}
