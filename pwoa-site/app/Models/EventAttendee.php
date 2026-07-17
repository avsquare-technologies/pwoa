<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'order_id',
        'ticket_id',
        'token',
        'status',
        'expires_at',
        'checked_in_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
