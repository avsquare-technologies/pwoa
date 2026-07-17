<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DynamicEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $data;
    public $parsedSubject;
    public $parsedContent;

    /**
     * Create a new message instance.
     */
    public function __construct(\App\Models\EmailTemplate $template, array $data = [])
    {
        $this->template = $template;
        $this->data = $data;

        $this->parsedSubject = $this->parseVariables($this->template->subject);
        $this->parsedContent = $this->parseVariables($this->template->content);
    }

    private function parseVariables($text)
    {
        if (!$text) return '';
        foreach ($this->data as $key => $value) {
            $text = str_replace('{' . $key . '}', $value ?? '', $text);
        }
        return $text;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->parsedSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.master',
            with: [
                'subject' => $this->parsedSubject,
                'content' => $this->parsedContent,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
