<?php

namespace App\Filament\Resources\Wallets;

use App\Filament\Resources\Wallets\Pages\ManageWallets;
use App\Models\Wallet;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'address';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('address')
                    ->disabled()
                    ->placeholder('Automatically generated'),
                TextInput::make('public_key')
                    ->disabled()
                    ->placeholder('Automatically generated'),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required()
                    ->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->fontFamily('mono')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('check_balance')
                    ->label('Check Balance')
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->action(function (Wallet $record, \App\Services\PublicWalletService $service) {
                        $balances = $service->getLedgerBalances($record);
                        
                        if (empty($balances)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body('Could not fetch balance from Ledger. The account might not be activated yet.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $balanceStr = collect($balances)
                            ->map(fn ($b) => "{$b['balance']} {$b['currency']}")
                            ->join(', ');

                        \Filament\Notifications\Notification::make()
                            ->title('Wallet Balance')
                            ->body("Balances for {$record->address}: " . $balanceStr)
                            ->success()
                            ->persistent()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWallets::route('/'),
        ];
    }
}
