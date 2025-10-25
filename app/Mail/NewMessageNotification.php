<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(Message $message, User $recipient)
    {
        $this->message = $message;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $senderName = $this->message->user->name ?? 'QuickChat';
        
        return new Envelope(
            subject: "Nouveau message de {$senderName} sur QuickChat",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-message-notification',
            with: [
                'message' => $this->message,
                'recipient' => $this->recipient,
                'senderName' => $this->message->user->name ?? 'Utilisateur',
                'messageContent' => $this->message->content,
                'conversationName' => $this->getConversationName(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get conversation name for display
     */
    private function getConversationName(): string
    {
        $conversation = $this->message->conversation;
        
        if ($conversation->is_group) {
            return $conversation->name_group ?? 'Groupe';
        }
        
        return $this->message->user->name ?? 'Conversation privée';
    }
}
