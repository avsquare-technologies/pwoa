<?php

namespace App\Actions\Auth;

use App\Models\User;

class SuspendUser
{
    public function execute(User $user): User
    {
        $user->update(['status' => 'suspended']);

        // Invalidate sessions via event or listener in a real app,
        // For now, the EnsureUserIsActive middleware handles logging out
        // suspended users on their next request.

        return $user;
    }
}
