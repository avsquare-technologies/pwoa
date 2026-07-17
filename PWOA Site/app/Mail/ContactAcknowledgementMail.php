<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactAcknowledgementMail extends Mailable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We have received your message - PWOA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.acknowledgement',
            with: [
                'data' => $this->data,
            ],
        );
    }
}
