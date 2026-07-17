<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'stripe_payment_id',
        'stripe_invoice_id',
        'amount',
        'currency',
        'status',
        'description',
        'payment_method',
        'card_last_four',
        'receipt_url',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTxHashAttribute()
    {
        if ($this->description !== '$WASH Token Purchase') {
            return null;
        }

        return \App\Models\TokenTransaction::where('user_id', $this->user_id)
            ->where('amount', $this->amount)
            ->where('status', 'success')
            ->whereBetween('created_at', [
                $this->created_at->copy()->subMinutes(10),
                $this->created_at->copy()->addMinutes(10)
            ])
            ->value('tx_hash');
    }
}
