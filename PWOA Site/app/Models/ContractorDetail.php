<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorDetail extends Model
{
    protected $fillable = [
        'business_id',
        'years_in_business',
        'license_number',
        'license_path',
        'license_status',
        'is_insured',
        'insurance_path',
        'insurance_status',
        'service_radius_id',
        'is_emergency_service',
        'is_subcontracting',
        'is_national_accounts',
    ];

    protected $casts = [
        'is_insured' => 'boolean',
        'is_emergency_service' => 'boolean',
        'is_subcontracting' => 'boolean',
        'is_national_accounts' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function serviceRadius(): BelongsTo
    {
        return $this->belongsTo(ServiceRadius::class, 'service_radius_id');
    }
}
