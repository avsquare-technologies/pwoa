<?php

namespace App\Filament\Resources\DirectoryCertificationResource\Pages;

use App\Filament\Resources\DirectoryCertificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDirectoryCertifications extends ManageRecords
{
    protected static string $resource = DirectoryCertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
