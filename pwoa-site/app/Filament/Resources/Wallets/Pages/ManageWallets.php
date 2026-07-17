<?php

namespace App\Filament\Resources\Wallets\Pages;

use App\Filament\Resources\Wallets\WalletResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWallets extends ManageRecords
{
    protected static string $resource = WalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(function (array $data): \App\Models\Wallet {
                    $user = \App\Models\User::findOrFail($data['user_id']);
                    
                    $action = app(\App\Actions\Wallet\CreateWalletAction::class);
                    $wallet = $action->execute($user);

                    if (! $wallet) {
                        throw new \Exception('Failed to create wallet via Private API');
                    }

                    return $wallet;
                }),
        ];
    }
}
