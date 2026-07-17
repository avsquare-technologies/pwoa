<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('stripe_payment_id'),
                TextInput::make('stripe_invoice_id'),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('USD'),
                Select::make('status')
                    ->options([
                        'succeeded' => 'Succeeded',
                        'failed' => 'Failed',
                        'pending' => 'Pending',
                        'refunded' => 'Refunded',
                    ])
                    ->required(),
                TextInput::make('description'),
                TextInput::make('payment_method'),
                TextInput::make('card_last_four'),
                TextInput::make('receipt_url')
                    ->url(),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
