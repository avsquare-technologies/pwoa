<?php

namespace App\Services\Shared;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendToUser(User $user, Notification $notification)
    {
        try {
            $user->notify($notification);
        } catch (\Exception $e) {
            Log::error('Notification Failed for User ID '.$user->id.': '.$e->getMessage());
        }
    }
}
