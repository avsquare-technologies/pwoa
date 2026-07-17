<?php

namespace App\Filament\Resources\DirectoryEquipmentResource\Pages;

use App\Filament\Resources\DirectoryEquipmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDirectoryEquipments extends ManageRecords
{
    protected static string $resource = DirectoryEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
