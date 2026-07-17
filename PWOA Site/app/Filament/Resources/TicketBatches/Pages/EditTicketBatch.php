<?php

namespace App\Filament\Resources\TicketBatches\Pages;

use App\Filament\Resources\TicketBatches\TicketBatchResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTicketBatch extends EditRecord
{
    protected static string $resource = TicketBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
