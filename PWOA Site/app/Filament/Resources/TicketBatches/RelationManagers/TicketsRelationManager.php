<?php

namespace App\Filament\Resources\TicketBatches\RelationManagers;

use App\Models\EventTicket;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $recordTitleAttribute = 'ticket_number';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('ticket_number')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('nft_token_id'),
                \Filament\Forms\Components\TextInput::make('tx_hash'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ticket_seq')
                    ->label('Seq')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nft_token_id')
                    ->label('NFT Token ID')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tx_hash')
                    ->label('Transaction')
                    ->copyable()
                    ->formatStateUsing(fn (?string $state): string => $state ? substr($state, 0, 8) . '...' : '-')
                    ->url(fn (EventTicket $record): ?string => $record->tx_hash ? str_replace('/account', '', config('services.xrpl.explorer_url')) . '/tx/' . $record->tx_hash : null, true),
                Tables\Columns\TextColumn::make('owner_wallet_address')
                    ->label('Owner Wallet')
                    ->copyable()
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'valid' => 'success',
                        'minted' => 'success',
                        'sold' => 'success',
                        'minting' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
