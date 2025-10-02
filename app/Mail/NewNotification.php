<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Creation message.
     */
    public function __construct()
    {
        //
    }

    /**
     * template email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Notification',
        );
    }

    /**
     * sujet email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * donnes a afficher
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
