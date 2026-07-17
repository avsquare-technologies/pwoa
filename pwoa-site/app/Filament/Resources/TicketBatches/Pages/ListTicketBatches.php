<?php

namespace App\Filament\Resources\TicketBatches\Pages;

use App\Filament\Resources\TicketBatches\TicketBatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTicketBatches extends ListRecords
{
    protected static string $resource = TicketBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
