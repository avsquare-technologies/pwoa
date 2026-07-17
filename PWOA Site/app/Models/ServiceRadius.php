<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRadius extends Model
{
    protected $table = 'service_radii';

    protected $fillable = [
        'name',
        'value',
        'slug',
        'description',
    ];

    public function contractorDetails(): HasMany
    {
        return $this->hasMany(ContractorDetail::class, 'service_radius_id');
    }
}
