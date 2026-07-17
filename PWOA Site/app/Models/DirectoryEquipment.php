<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DirectoryEquipment extends Model
{
    protected $table = 'directory_equipments';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_path',
    ];

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_directory_equipment')
            ->withPivot(['quantity', 'specifications', 'is_verified', 'verification_photo_path'])
            ->withTimestamps();
    }
}
