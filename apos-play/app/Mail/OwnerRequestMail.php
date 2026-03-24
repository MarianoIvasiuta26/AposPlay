<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OwnerRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $complexName,
        public string $complexCity,
        public string $complexAddress,
        public string $complexCourts,
        public string $contactName,
        public string $contactEmail,
        public string $contactPhone,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->contactEmail],
            subject: 'Solicitud cuenta Owner - ' . $this->complexName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.owner-request',
        );
    }
}
