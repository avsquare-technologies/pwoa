<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'inquiry_type',
        'name',
        'email',
        'subject',
        'message',
        'phone',
        'company',
    ];
}
