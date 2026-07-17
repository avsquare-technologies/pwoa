<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CountryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('iso3')
                    ->placeholder('-'),
                TextEntry::make('iso2')
                    ->placeholder('-'),
                TextEntry::make('phone_code')
                    ->placeholder('-'),
                TextEntry::make('capital')
                    ->placeholder('-'),
                TextEntry::make('currency')
                    ->placeholder('-'),
                TextEntry::make('native')
                    ->placeholder('-'),
                TextEntry::make('region')
                    ->placeholder('-'),
                TextEntry::make('subregion')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
