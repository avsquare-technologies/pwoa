<?php

namespace App\Filament\Resources\TicketBatches\Tables;

use App\Models\TicketBatch;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketBatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->columns([
                TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable(),
                TextColumn::make('sold_count')
                    ->label('Sold')
                    ->getStateUsing(fn (TicketBatch $record) => min($record->total, $record->tickets()->where(function ($q) {
                        $q->whereIn('status', ['sold', 'minting'])
                          ->orWhere(function ($sq) {
                              $sq->where('status', 'minted')
                                 ->whereNotNull('user_id');
                          });
                    })->count()))
                    ->badge()
                    ->color('info'),
                TextColumn::make('minted')
                    ->label('Minted')
                    ->getStateUsing(fn (TicketBatch $record) => min($record->total, $record->minted))
                    ->badge()
                    ->color('success'),
                TextColumn::make('pending_count')
                    ->label('Pending')
                    ->getStateUsing(fn (TicketBatch $record) => max(0, $record->total - $record->minted))
                    ->badge()
                    ->color('warning'),
                TextColumn::make('progress')
                    ->label('Minting Progress')
                    ->view('filament.columns.ticket-progress-bar'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'queued' => 'gray',
                        'minting' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('last_heartbeat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\Action::make('refresh')
                    ->label('Refresh')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->action(fn () => null), // Polling takes care of it, but this gives visual feedback
            ])
            ->actions([
                /*
                // Manual minting is disabled because the project now uses on-demand NFT minting.
                \Filament\Actions\Action::make('run_minting')
                    ->label('Run Minting')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->hidden(fn (TicketBatch $record) => $record->status === 'completed' || $record->status === 'minting')
                    ->action(function (TicketBatch $record) {
                        \App\Jobs\DispatchTicketChunksJob::dispatch(
                            batchId: $record->batch_id,
                            chunkSize: 2
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Job Re-dispatched')
                            ->body('The minting process has been added back to the queue.')
                            ->success()
                            ->send();
                    }),
                */
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
