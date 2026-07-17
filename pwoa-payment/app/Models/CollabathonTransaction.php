<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollabathonTransaction extends Model
{
    use HasFactory;
    protected $table = 'collabathon_transactions';

    protected $fillable = [
        'collabathon_id',
        'type',
        'tx_hash',
        'source_address',
        'destination_address',
        'amount',
        'nft_token_id',
        'status',
        'response',
        'submitted_at',
        'validated_at',
    ];

    protected $casts = [
        'response' => 'array',
        'submitted_at' => 'datetime',
        'validated_at' => 'datetime',
        'amount' => 'float',
    ];
}
