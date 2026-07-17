<?php

namespace App\Services\Shared;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public function log(string $action, $model = null, array $oldValues = [], array $newValues = [], ?User $actorUser = null)
    {
        $userId = $actorUser ? $actorUser->id : auth()->id();

        // Ensure user_id is null if the user doesn't exist (e.g. invalid session ID)
        if ($userId && !auth()->check() && !$actorUser) {
            $userId = null;
        }

        return AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'target_type' => $model ? get_class($model) : null,
            'target_id' => $model ? $model->getKey() : null,
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
