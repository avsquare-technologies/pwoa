<?php

namespace App\Actions\Auth;

use App\Models\User;

class BanUser
{
    public function execute(User $user): User
    {
        $user->update(['status' => 'banned']);

        return $user;
    }
}
