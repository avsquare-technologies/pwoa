<?php

namespace App\Filament\Resources\Certificates\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('certificate_number')
                    ->label('Certificate #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('score')
                    ->label('Score')
                    ->suffix('%')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state >= 90 ? 'success' : ($state >= 70 ? 'warning' : 'danger')),
                TextColumn::make('nft_status')
                    ->label('NFT Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'minted' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('ipfs_image_hash')
                    ->label('NFT Image')
                    ->formatStateUsing(fn ($state) => $state ? 'View NFT' : '-')
                    ->icon(fn ($state) => $state ? 'heroicon-o-photo' : null)
                    ->url(fn ($record) => $record->ipfs_image_url)
                    ->openUrlInNewTab()
                    ->color('info'),
                TextColumn::make('nft_token_id')
                    ->label('Explorer')
                    ->formatStateUsing(fn ($state) => $state ? 'View on XRPL' : '-')
                    ->url(fn ($record) => $record->nft_token_id ? str_replace('/account', '', rtrim(config('services.xrpl.explorer_url', 'https://testnet.xrpl.org'), '/')) . "/nft/{$record->nft_token_id}" : null)
                    ->openUrlInNewTab()
                    ->color('primary'),
                TextColumn::make('issued_at')
                    ->label('Issued')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('course')
                    ->relationship('course', 'title')
                    ->label('Course'),
            ])
            ->defaultSort('issued_at', 'desc');
    }
}
