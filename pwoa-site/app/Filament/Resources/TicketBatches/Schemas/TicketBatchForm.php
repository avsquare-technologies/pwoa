<?php

namespace App\Filament\Resources\TicketBatches\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TicketBatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->relationship('event', 'title')
                    ->required(),
                TextInput::make('creator_id')
                    ->numeric(),
                TextInput::make('batch_id')
                    ->required(),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('minted')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('failed')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('next_index')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('metadata_uri'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('status')
                    ->required()
                    ->default('queued'),
                Textarea::make('error')
                    ->columnSpanFull(),
                DateTimePicker::make('last_heartbeat'),
            ]);
    }
}
