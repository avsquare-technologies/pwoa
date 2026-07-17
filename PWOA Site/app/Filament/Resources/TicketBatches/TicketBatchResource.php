<?php

namespace App\Filament\Resources\TicketBatches;

use App\Filament\Resources\TicketBatches\Pages\CreateTicketBatch;
use App\Filament\Resources\TicketBatches\Pages\EditTicketBatch;
use App\Filament\Resources\TicketBatches\Pages\ListTicketBatches;
use App\Filament\Resources\TicketBatches\Pages\ViewTicketBatch;
use App\Filament\Resources\TicketBatches\Schemas\TicketBatchForm;
use App\Filament\Resources\TicketBatches\Schemas\TicketBatchInfolist;
use App\Filament\Resources\TicketBatches\Tables\TicketBatchesTable;
use App\Models\TicketBatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TicketBatchResource extends Resource
{
    protected static ?string $model = TicketBatch::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Events & Programs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $recordTitleAttribute = 'batch_id';

    public static function form(Schema $schema): Schema
    {
        return TicketBatchForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketBatchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketBatchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TicketsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTicketBatches::route('/'),
            'create' => CreateTicketBatch::route('/create'),
            'view' => ViewTicketBatch::route('/{record}'),
            'edit' => EditTicketBatch::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['queued', 'minting', 'failed'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'failed')->exists() ? 'danger' : 'warning';
    }
}
