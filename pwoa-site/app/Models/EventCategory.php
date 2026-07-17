<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function events(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Event::class);
    }
}
