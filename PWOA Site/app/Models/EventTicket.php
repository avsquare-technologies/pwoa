<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'batch_id',
        'order_id',
        'ticket_seq',
        'ticket_number',
        'owner_wallet_address',
        'nft_token_id',
        'tx_hash',
        'status',
        'reserved_at',
        'reserved_by_user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function order(): BelongsTo 
    {
        return $this->belongsTo(TicketOrder::class, 'order_id');
    }

    public function scopeAvailable($query) 
    {
        return $query->where('status', 'minted')
                     ->whereNull('user_id')
                     ->whereNull('order_id');
    }
}
