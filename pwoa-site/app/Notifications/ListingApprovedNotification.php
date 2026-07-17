<?php

namespace App\Notifications;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $business;

    public function __construct(Business $business)
    {
        $this->business = $business;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $template = \App\Models\EmailTemplate::where('type', 'business_approved')->where('is_active', true)->first();
        if ($template) {
            return (new \App\Mail\DynamicEmail($template, [
                'user_name' => $notifiable->name,
                'business_name' => $this->business->name,
                'profile_link' => route('directory'),
            ]))->to($notifiable->routeNotificationFor('mail') ?? $notifiable->email);
        }

        return (new MailMessage)
            ->subject('Your Directory Listing Has Been Approved!')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! Your business listing "' . $this->business->name . '" has been reviewed and approved by our directory administration team.')
            ->line('Your business profile is now active on the public directory page.')
            ->action('View Directory', route('directory'))
            ->line('Thank you for being a member of the Power Washing Of America directory.');
    }

    public function toArray($notifiable): array
    {
        return [
            'business_id' => $this->business->id,
            'business_name' => $this->business->name,
            'status' => 'approved',
            'message' => 'Your business listing "' . $this->business->name . '" has been approved!',
        ];
    }
}
