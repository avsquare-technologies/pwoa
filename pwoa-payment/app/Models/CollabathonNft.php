<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollabathonNft extends Model
{
    use HasFactory;
    protected $table = 'collabathon_nfts';

    protected $fillable = [
        'batch_id',
        'creator_address',
        'ticket_index',
        'ticket_sequence',
        'tx_hash',
        'nft_token_id',
        'status',
    ];

    protected $casts = [
        'ticket_index' => 'integer',
        'ticket_sequence' => 'string',
    ];
}