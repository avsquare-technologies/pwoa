<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorDetail extends Model
{
    protected $fillable = [
        'business_id',
        'years_in_business',
        'has_online_ordering',
        'has_local_pickup',
        'has_member_discounts',
        'wants_preferred_program',
        'wants_partnership',
    ];

    protected $casts = [
        'has_online_ordering' => 'boolean',
        'has_local_pickup' => 'boolean',
        'has_member_discounts' => 'boolean',
        'wants_preferred_program' => 'boolean',
        'wants_partnership' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
