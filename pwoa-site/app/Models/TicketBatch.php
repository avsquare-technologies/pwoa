<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketBatch extends Model
{
    use HasFactory;

    protected $table = "ticket_batches";

    protected $fillable = [
        "event_id",
        "creator_id",
        "batch_id",
        "total",
        "minted",
        "failed",
        "next_index",
        "metadata_uri",
        "price",
        "status",
        "error",
        "last_heartbeat"
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EventTicket::class, 'batch_id', 'batch_id');
    }
}
