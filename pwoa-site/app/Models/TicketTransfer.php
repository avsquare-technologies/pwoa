<?php

namespace App\Models;

use App\Enums\TransferStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketTransfer extends Model
{
    protected $fillable = [
        'order_id', 
        'ticket_id', 
        'nft_token_id', 
        'tx_hash', 
        'status', 
        'error_message'
    ];

    protected $casts = [
        'status' => TransferStatus::class,
    ];

    public function order(): BelongsTo 
    {
        return $this->belongsTo(TicketOrder::class, 'order_id');
    }

    public function ticket(): BelongsTo 
    {
        return $this->belongsTo(EventTicket::class);
    }
}
