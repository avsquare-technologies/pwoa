<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tx_hash',
        'source',
        'destination',
        'amount',
        'currency',
        'type',
        'status',
        'response',
        'submitted_at',
        'validated_at',
    ];

    protected $casts = [
        'response' => 'array',
        'submitted_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

}