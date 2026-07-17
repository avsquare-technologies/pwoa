<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nft extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_address',
        'original_creator_address',
        'nft_id',
        'taxon',
        'uri',
        'flags',
        'transfer_fee',
        'status',
        'tx_hash',
        'response',
        'error_message',
    ];

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_MINTED = 'MINTED';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_CONFIRMED = 'CONFIRMED';

    protected $guarded = ['id'];

    protected $casts = [
        'taxon' => 'integer',
        'flags' => 'integer',
        'transfer_fee' => 'integer',
        'response' => 'array',
    ];

    public function isBurnable(): bool
    {
        return ($this->flags & 1) !== 0;
    }

    public function isOnlyXrp(): bool
    {
        return ($this->flags & 2) !== 0;
    }

    public function isTransferable(): bool
    {
        return ($this->flags & 8) !== 0;
    }

    public function getRoyaltyPercentageAttribute(): float
    {
        return $this->transfer_fee / 1000;
    }

    public function getDecodedUriAttribute(): ?string
    {
        if (ctype_xdigit($this->uri)) {
            return hex2bin($this->uri);
        }
        return $this->uri;
    }
}