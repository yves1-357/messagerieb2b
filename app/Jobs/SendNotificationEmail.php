<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle notification - QuickChat',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
