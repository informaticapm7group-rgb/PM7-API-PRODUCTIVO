<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $lead)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('[%s] Nueva solicitud de contacto — %s', $this->lead['tracking_number'], $this->lead['name']),
            replyTo: [$this->lead['email']],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
        );
    }
}
