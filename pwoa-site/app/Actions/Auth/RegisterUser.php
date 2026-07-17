<?php

namespace App\Actions\Auth;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RegisterUser
{
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => 'active',
            ]);

            if (Role::where('name', 'user')->exists()) {
                $user->assignRole('user');
            }

            $user->detail()->create();

            // Create Wallet via Action
            app(\App\Actions\Wallet\CreateWalletAction::class)->execute($user);

            event(new Registered($user));
            event(new UserRegistered($user));

            return $user;
        });
    }
}
