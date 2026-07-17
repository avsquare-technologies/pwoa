<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketOrder extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 
        'event_id', 
        'quantity', 
        'total_amount', 
        'currency', 
        'status', 
        'error_message', 
        'metadata'
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'metadata' => 'array',
    ];

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo 
    {
        return $this->belongsTo(Event::class);
    }

    public function transfers(): HasMany 
    {
        return $this->hasMany(TicketTransfer::class, 'order_id');
    }

    public function attendees(): HasMany 
    {
        return $this->hasMany(EventAttendee::class, 'order_id', 'id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(EventTicket::class, 'order_id', 'id');
    }
}
