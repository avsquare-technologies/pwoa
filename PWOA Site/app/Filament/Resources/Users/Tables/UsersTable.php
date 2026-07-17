<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('wallet.address')
                    ->label('Wallet Address')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->placeholder('No wallet'),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('membership_status')
                    ->label('Membership')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->isActiveMember() ? 'Active' : 'Inactive')
                    ->color(fn ($state) => $state === 'Active' ? 'success' : 'gray'),
                TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('active_members')
                    ->query(fn (Builder $query) => $query->whereHas('membershipStatus', fn ($q) => $q->where('status', 'active')))
                    ->label('Active Members Only'),
            ])
            ->recordActions([
                 \Filament\Actions\Action::make('changePassword')
        ->label('')
        ->icon('heroicon-o-key')
        ->iconButton()
        ->tooltip('Change Password')
        ->color('warning')

        ->modalHeading('Change Password')

        ->form([

            \Filament\Forms\Components\TextInput::make('password')
                ->label('New Password')
                ->password()
                ->revealable()
                ->required()
                ->minLength(8),

            \Filament\Forms\Components\TextInput::make('password_confirmation')
                ->label('Confirm Password')
                ->password()
                ->revealable()
                ->required()
                ->same('password'),

        ])

        ->action(function (array $data, $record): void {

            $record->update([
                'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            ]);

            \Filament\Notifications\Notification::make()
                ->title('Password changed successfully')
                ->success()
                ->send();
        }),


                \Filament\Actions\Action::make('generate_wallet')
                    ->label('Generate Wallet')
                    ->icon('heroicon-o-wallet')
                    ->color('success')
                    ->hidden(fn ($record) => $record->wallet !== null)
                    ->action(function ($record, \App\Actions\Wallet\CreateWalletAction $createWalletAction) {
                        $wallet = $createWalletAction->execute($record);

                        if ($wallet) {
                            \Filament\Notifications\Notification::make()
                                ->title('Wallet generated successfully')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Failed to generate wallet')
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation(),
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
