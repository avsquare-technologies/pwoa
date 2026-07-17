<?php

namespace App\Filament\Resources\MembershipStatuses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MembershipStatusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('plan')
                    ->required()
                    ->default('annual'),
                DatePicker::make('started_at'),
                DatePicker::make('expires_at'),
                Toggle::make('auto_renew')
                    ->required(),
                DateTimePicker::make('cancelled_at'),
            ]);
    }
}
