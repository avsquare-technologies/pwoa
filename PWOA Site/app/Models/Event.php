<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'slug',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'capacity',
        'price',
        'is_free_for_members',
        'image_path',
        'status',
        'event_category_id',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'price' => 'decimal:2',
        'is_free_for_members' => 'boolean',
    ];

    /**
     * Get the attendees for the event.
     */
    public function attendees(): HasMany
    {
        return $this->hasMany(EventAttendee::class);
    }

    /**
     * Get the minted tickets for the event.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(EventTicket::class);
    }

    /**
     * Get the ticket batches for the event.
     */
    public function batches(): HasMany
    {
        return $this->hasMany(TicketBatch::class);
    }

    /**
     * Check if the event is free for the given user.
     */
    public function isFreeFor(User $user): bool
    {
        if ($this->price <= 0) {
            return true;
        }

        if ($this->is_free_for_members && $user->isActiveMember()) {
            return true;
        }

        return false;
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function remainingSeats(): int
    {
        if ($this->capacity === null) {
            return 9999;
        }
        $soldOrMintingCount = $this->tickets()
            ->where(function ($q) {
                $q->whereIn('status', ['sold', 'minting'])
                  ->orWhere(function ($sq) {
                      $sq->where('status', 'minted')
                         ->whereNotNull('user_id');
                  });
            })
            ->count();
        return max(0, $this->capacity - $soldOrMintingCount);
    }

    public function isSoldOut(): bool
    {
        return $this->remainingSeats() <= 0;
    }

    /**
     * Check if the event has already ended or started.
     */
    public function isEnded(): bool
    {
        // If there's an end date, use it. Otherwise use the start date.
        if ($this->ends_at) {
            return $this->ends_at->isPast();
        }

        return $this->starts_at->isPast();
    }
}
