<?php

namespace App\Filament\Resources\TicketBatches\Schemas;

use App\Models\TicketBatch;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TicketBatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('General Information')
                    ->schema([
                        TextEntry::make('event.title')
                            ->label('Event'),
                        TextEntry::make('batch_id')
                            ->label('Batch ID')
                            ->copyable(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'queued' => 'gray',
                                'minting' => 'warning',
                                'completed' => 'success',
                                'failed' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('success_msg')
                            ->label('')
                            ->default('🎉 Batch Minting Completed Successfully!')
                            ->weight('bold')
                            ->color('success')
                            ->hidden(fn (TicketBatch $record) => $record->status !== 'completed')
                            ->columnSpanFull(),
                        TextEntry::make('price')
                            ->money('USD'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Minting Statistics')
                    ->schema([
                        \Filament\Infolists\Components\ViewEntry::make('progress')
                            ->label('Live Progress')
                            ->view('filament.columns.ticket-progress-bar')
                            ->columnSpanFull(),
                        TextEntry::make('total')
                            ->numeric(),
                        TextEntry::make('minted')
                            ->numeric()
                            ->color('success'),
                        TextEntry::make('failed')
                            ->numeric()
                            ->color('danger'),
                        TextEntry::make('next_index')
                            ->label('Next Index')
                            ->numeric(),
                    ])->columns(4),

                \Filament\Schemas\Components\Section::make('System Details')
                    ->schema([
                        TextEntry::make('metadata_uri')
                            ->label('IPFS Metadata URI')
                            ->url(fn (?string $state): ?string => $state ? str_replace('ipfs://', 'https://gateway.pinata.cloud/ipfs/', $state) : null, true)
                            ->placeholder('-'),
                        TextEntry::make('last_heartbeat')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('error')
                            ->placeholder('No errors reported')
                            ->columnSpanFull()
                            ->color('danger'),
                    ])->columns(2),
            ]);
    }
}
