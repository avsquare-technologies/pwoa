<?php

namespace App\Filament\Resources\MembershipStatuses;

use App\Filament\Resources\MembershipStatuses\Pages\ListMembershipStatuses;
use App\Filament\Resources\MembershipStatuses\Schemas\MembershipStatusForm;
use App\Filament\Resources\MembershipStatuses\Tables\MembershipStatusesTable;
use App\Models\MembershipStatus;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MembershipStatusResource extends Resource
{
    protected static ?string $model = MembershipStatus::class;

    protected static string | UnitEnum | null $navigationGroup = 'Member Services';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedIdentification;

    public static function form(Schema $schema): Schema
    {
        return MembershipStatusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MembershipStatusesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMembershipStatuses::route('/'),
        ];
    }
}
