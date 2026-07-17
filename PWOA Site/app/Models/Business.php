<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'type',
        'status',
        'tagline',
        'short_description',
        'description',
        'email',
        'phone',
        'website',
        'logo_path',
        'cover_photo_path',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'zip',
        'membership_tier',
        'is_verified',
        'is_preferred',
        'views_count',
        'avg_rating',
        'facebook',
        'instagram',
        'linkedin',
        'youtube',
        'tiktok',
        'verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'is_preferred' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function contractorDetail(): HasOne
    {
        return $this->hasOne(ContractorDetail::class);
    }

    public function vendorDetail(): HasOne
    {
        return $this->hasOne(VendorDetail::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BusinessCategory::class, 'business_category_business');
    }

    public function directoryCertifications(): BelongsToMany
    {
        return $this->belongsToMany(DirectoryCertification::class, 'business_directory_certification')
            ->withPivot(['certificate_number', 'issued_at', 'expires_at', 'status', 'document_path'])
            ->withTimestamps();
    }

    public function directoryEquipments(): BelongsToMany
    {
        return $this->belongsToMany(DirectoryEquipment::class, 'business_directory_equipment')
            ->withPivot(['quantity', 'specifications', 'is_verified', 'verification_photo_path'])
            ->withTimestamps();
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'badge_business')
            ->withPivot(['assigned_at', 'expires_at'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeContractors($query)
    {
        return $query->where('type', 'contractor');
    }

    public function scopeVendors($query)
    {
        return $query->where('type', 'vendor');
    }

    public function scopeGold($query)
    {
        return $query->where('membership_tier', 'gold');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePreferred($query)
    {
        return $query->where('is_preferred', true);
    }

    /**
     * Backward compatibility accessor for templates using business_name.
     */
    public function getBusinessNameAttribute(): string
    {
        return $this->name;
    }
}

