<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->description('Primary user account details.')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn ($context) => $context === 'create'),
                    ])->columns(3),

                Section::make('Account Security & Status')
                    ->schema([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Select::make('status')
                            ->options(['active' => 'Active', 'suspended' => 'Suspended', 'banned' => 'Banned'])
                            ->default('active')
                            ->required(),
                        DateTimePicker::make('email_verified_at'),
                        DateTimePicker::make('last_login_at')
                            ->disabled(),
                    ])->columns(2),

                Section::make('Stripe & Billing Data')
                    ->collapsed()
                    ->schema([
                        TextInput::make('stripe_id')
                            ->label('Stripe Customer ID'),
                        TextInput::make('pm_type')
                            ->label('Payment Method Type'),
                        TextInput::make('pm_last_four')
                            ->label('Card Last 4'),
                        DateTimePicker::make('trial_ends_at'),
                    ])->columns(2),
            ]);
    }
}
