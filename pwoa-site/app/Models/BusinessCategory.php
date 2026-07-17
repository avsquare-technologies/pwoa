<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BusinessCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'type',
        'parent_id',
        'category_type',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BusinessCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(BusinessCategory::class, 'parent_id');
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_category_business');
    }
}

