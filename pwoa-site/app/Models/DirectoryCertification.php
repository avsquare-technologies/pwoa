<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DirectoryCertification extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'badge_icon_path',
    ];

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_directory_certification')
            ->withPivot(['certificate_number', 'issued_at', 'expires_at', 'status'])
            ->withTimestamps();
    }
}
