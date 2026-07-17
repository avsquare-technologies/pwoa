<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'user_id',
        'admin_id',
        'message',
    ];

    protected static function booted()
    {
        static::created(function ($reply) {
            $complaint = $reply->complaint;
            
            // If admin replies, notify user
            if ($reply->admin_id) {
                $complaint->user->notify(new \App\Notifications\NewComplaintReply($complaint, $reply));
            } 
            // If user replies, notify assigned admin (if any)
            elseif ($reply->user_id && $complaint->assigned_to) {
                $complaint->assignee->notify(new \App\Notifications\NewComplaintReply($complaint, $reply));
            }
        });
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the sender of the reply.
     */
    public function sender()
    {
        return $this->admin_id ? $this->admin : $this->user;
    }
}
