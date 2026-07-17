<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderNft extends Model
{
    protected $fillable = [
        'nft_db_id',
        'nft_token_id',
        'order_id',
        'buyer_address',
        'buyer_id',
        'seller_address',
        'seller_id',
        'amount',
        'tx_hash',
        'status'
    ];
}