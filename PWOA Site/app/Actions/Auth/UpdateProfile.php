<?php

namespace App\Actions\Auth;

use App\Models\User;

class UpdateProfile
{
    public function execute(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'] ?? $user->name,
        ]);

        $user->detail()->update([
            'phone' => $data['phone'] ?? $user->detail->phone,
            'country_id' => $data['country_id'] ?? $user->detail->country_id,
            'state_id' => $data['state_id'] ?? $user->detail->state_id,
            'city_id' => $data['city_id'] ?? $user->detail->city_id,
            'address' => $data['address'] ?? $user->detail->address,
            'zip' => $data['zip'] ?? $user->detail->zip,
            'date_of_birth' => $data['date_of_birth'] ?? $user->detail->date_of_birth,
        ]);

        return $user->refresh();
    }
}
