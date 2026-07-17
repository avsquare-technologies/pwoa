<?php

namespace App\Filament\Resources\Certificates;

use App\Filament\Resources\Certificates\Tables\CertificatesTable;
use App\Models\Certificate;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static string | UnitEnum | null $navigationGroup = 'Learning Center';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    public static function table(Table $table): Table
    {
        return CertificatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificates::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
