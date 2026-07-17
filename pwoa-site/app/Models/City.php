<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    protected $fillable = [
        'id',
        'name',
        'state_id',
        'state_code',
        'country_id',
        'country_code',
        'latitude',
        'longitude',
        'flag',
        'wikiDataId',
    ];

    public $incrementing = false;

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function businesses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Business::class);
    }
}
