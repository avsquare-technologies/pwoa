<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ComplaintStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Complaint $complaint)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'complaint_id' => $this->complaint->id,
            'ticket_id' => $this->complaint->ticket_id,
            'status' => $this->complaint->status->getLabel(),
            'message' => "Your complaint {$this->complaint->ticket_id} status has been updated to {$this->complaint->status->getLabel()}.",
            'action_url' => route('complaints.show', $this->complaint),
        ];
    }
}
