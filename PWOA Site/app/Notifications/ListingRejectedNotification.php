<?php

namespace App\Notifications;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $business;
    protected $rejectionReason;

    public function __construct(Business $business, string $rejectionReason)
    {
        $this->business = $business;
        $this->rejectionReason = $rejectionReason;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $template = \App\Models\EmailTemplate::where('type', 'business_rejected')->where('is_active', true)->first();
        if ($template) {
            return (new \App\Mail\DynamicEmail($template, [
                'user_name' => $notifiable->name,
                'business_name' => $this->business->name,
                'rejection_reason' => $this->rejectionReason,
            ]))->to($notifiable->routeNotificationFor('mail') ?? $notifiable->email);
        }

        return (new MailMessage)
            ->subject('Updates Required: Your Directory Listing Request')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for submitting your business listing request for "' . $this->business->name . '".')
            ->line('During our review, the administration team determined that some details need to be corrected before approval:')
            ->line('**Reason for Revision:** ' . $this->rejectionReason)
            ->line('Please log in to your dashboard to make the necessary corrections and resubmit your profile.')
            ->action('Update Listing', route('business.manage'))
            ->line('If you have any questions, please contact our support team.');
    }

    public function toArray($notifiable): array
    {
        return [
            'business_id' => $this->business->id,
            'business_name' => $this->business->name,
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
            'message' => 'Your business listing request for "' . $this->business->name . '" was rejected. Reason: ' . $this->rejectionReason,
        ];
    }
}
