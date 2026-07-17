<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEscrow extends Model
{
    protected $fillable = [
        'buyer_address',
        'seller_address',
        'amount',
        'amount_usd',
        'order_id',
        'xrpl_account',
        'condition',
        'condition_secret',
        'escrow_sequence',
        'tx_hash',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'expires_at' => 'datetime',
    ];

    // Status constants (no magic strings)
    public const CREATED = 'created';
    public const FUNDED = 'funded';
    public const RELEASED = 'released';
    public const CANCELED = 'canceled';


    public function isActive(): bool
    {
        return in_array($this->status, [
            self::CREATED,
            self::FUNDED,
        ], true);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && now()->greaterThan($this->expires_at);
    }
}
