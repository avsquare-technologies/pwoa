<?php

namespace App\Notifications;

use App\Models\Complaint;
use App\Models\ComplaintReply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewComplaintReply extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Complaint $complaint, public ComplaintReply $reply)
    {
        // Property promotion takes care of assignment.
    }

    /**
     * Channels the notification will be sent through.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Data stored for the database notification channel.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $senderName = $this->reply->sender()?->name ?? 'Support Team';

        return new DatabaseMessage([
            'complaint_id' => $this->complaint->id,
            'ticket_id'    => $this->complaint->ticket_id,
            'message'      => "New reply from {$senderName} on ticket {$this->complaint->ticket_id}.",
            'attachment'   => $this->reply->attachment,
            'action_url'   => route('complaints.show', $this->complaint),
        ]);
    }

    /**
     * Backward‑compatible array representation for any legacy calls.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable)->data;
    }
}
