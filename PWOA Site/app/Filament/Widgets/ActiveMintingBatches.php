<?php

namespace App\Filament\Widgets;

use App\Models\TicketBatch;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\TicketBatches\TicketBatchResource;

class ActiveMintingBatches extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TicketBatch::query()->whereIn('status', ['queued', 'minting', 'failed'])->latest()
            )
            ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event'),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Minting Progress')
                    ->view('filament.columns.progress-bar'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'queued' => 'gray',
                        'minting' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Update')
                    ->dateTime()
                    ->since(),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->url(fn (TicketBatch $record): string => TicketBatchResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('view_all')
                    ->label('View All Batches')
                    ->url(TicketBatchResource::getUrl())
                    ->icon('heroicon-o-list-bullet')
                    ->color('gray'),
            ]);
    }

    public static function canView(): bool
    {
        return TicketBatch::whereIn('status', ['queued', 'minting', 'failed'])->exists();
    }
}
