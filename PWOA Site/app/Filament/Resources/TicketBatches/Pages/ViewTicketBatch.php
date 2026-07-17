<?php

namespace App\Filament\Resources\TicketBatches\Pages;

use App\Filament\Resources\TicketBatches\TicketBatchResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTicketBatch extends ViewRecord
{
    protected static string $resource = TicketBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            /*
            // Manual minting is disabled because the project now uses on-demand NFT minting.
            \Filament\Actions\Action::make('run_minting')
                ->label('Run Minting')
                ->icon('heroicon-o-play')
                ->color('success')
                ->hidden(fn () => $this->record->status === 'completed' || $this->record->status === 'minting')
                ->action(function () {
                    \App\Jobs\DispatchTicketChunksJob::dispatch(
                        batchId: $this->record->batch_id,
                        chunkSize: 2
                    );

                    \Filament\Notifications\Notification::make()
                        ->title('Job Re-dispatched')
                        ->body('The minting process has been added back to the queue.')
                        ->success()
                        ->send();
                }),
            */
            EditAction::make(),
        ];
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return "Minting Batch: {$this->record->batch_id}";
    }

    /**
     * Enable polling for the view page
     */
    protected function getPollingInterval(): ?string
    {
        return '5s';
    }
}
