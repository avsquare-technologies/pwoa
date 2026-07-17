<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RegisterOtpMail extends Mailable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Email Address - PWOA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.register-otp',
            with: [
                'data' => $this->data,
            ],
        );
    }
}
