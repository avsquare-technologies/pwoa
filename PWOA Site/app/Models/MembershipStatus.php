<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipStatus extends Model
{
    protected $fillable = [
        'user_id',
        'is_active',
        'plan',
        'started_at',
        'expires_at',
        'auto_renew',
        'cancelled_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_renew' => 'boolean',
        'started_at' => 'date',
        'expires_at' => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
