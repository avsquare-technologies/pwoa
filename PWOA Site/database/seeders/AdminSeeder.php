<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web'
        ]);

        $admins = [
            [
                'name' => 'Admin User',
                'email' => 'admin@pwoa.org',
                'password' => 'password123',
            ],
            [
                'name' => 'TY Admin',
                'email' => 'ty@pwoa.org',
                'password' => '12345678',
            ],
        ];

        foreach ($admins as $adminData) {

            $admin = User::firstOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'password' => Hash::make($adminData['password']),
                    'status' => 'active',
                ]
            );

            $admin->assignRole($superAdmin);

            if (! $admin->detail) {
                $admin->detail()->create();
            }

            // if (! $admin->wallet) {
            //     app(\App\Actions\Wallet\CreateWalletAction::class)
            //         ->execute($admin);
            // }
        }
    }
}
