<?php

namespace App\Models;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'category_id',
        'assigned_to',
        'attachment_path',
    ];

    protected $casts = [
        'status' => ComplaintStatus::class,
        'priority' => ComplaintPriority::class,
    ];

    protected static function booted()
    {
        static::creating(function ($complaint) {
            if (empty($complaint->ticket_id)) {
                $complaint->ticket_id = static::generateTicketId();
            }
        });

        static::updated(function ($complaint) {
            if ($complaint->isDirty('status')) {
                $complaint->user->notify(new \App\Notifications\ComplaintStatusChanged($complaint));
            }
        });
    }

    public static function generateTicketId(): string
    {
        $year = date('Y');
        $prefix = "PWOA-{$year}-";
        
        return DB::transaction(function () use ($prefix) {
            $lastComplaint = static::where('ticket_id', 'like', "{$prefix}%")
                ->orderBy('ticket_id', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastComplaint) {
                $lastNumber = (int) substr($lastComplaint->ticket_id, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ComplaintReply::class);
    }
}
