<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'source',
        'from_user_id',
        'to_user_id',
        'tx_hash',
        'destination',
        'amount',
        'type',
        'status',
        'response',
        'submitted_at',
    ];

    protected $casts = [
        'response' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
