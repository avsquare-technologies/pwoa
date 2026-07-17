<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'destination_address',
        'tx_hash',
        'status',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
