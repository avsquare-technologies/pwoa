<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SystemWallet extends Model
{
    protected $fillable = ['name', 'address','public_key', 'seed', 'type', 'default_currency'];
}
