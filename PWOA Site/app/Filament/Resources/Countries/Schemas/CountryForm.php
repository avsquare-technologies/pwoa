<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('iso3'),
                TextInput::make('iso2'),
                TextInput::make('phone_code')
                    ->tel(),
                TextInput::make('capital'),
                TextInput::make('currency'),
                TextInput::make('native'),
                TextInput::make('region'),
                TextInput::make('subregion'),
            ]);
    }
}
