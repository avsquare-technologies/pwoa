<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Config;
use RuntimeException;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'address',
        'seed',
        'public_key',
        'private_key',
        'status',
    ];

    protected $hidden = ['seed', 'private_key'];

    private function getWalletCipher(): Encrypter
    {
        $rawKey = Config::get('xrpl.wallet_key');

        if (! $rawKey) {
            throw new RuntimeException("WALLET_KEY is not set in 'config/xrpl.php'.");
        }

        if (str_starts_with($rawKey, 'base64:')) {
            $key = base64_decode(substr($rawKey, 7));
        } else {
            $key = $rawKey;
        }

        return new Encrypter($key, Config::get('app.cipher'));
    }

    protected function seed(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? $this->getWalletCipher()->decrypt($value) : null,
            set: fn ($value) => $value ? $this->getWalletCipher()->encrypt($value) : null,
        );
    }

    protected function privateKey(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? $this->getWalletCipher()->decrypt($value) : null,
            set: fn ($value) => $value ? $this->getWalletCipher()->encrypt($value) : null,
        );
    }
}
