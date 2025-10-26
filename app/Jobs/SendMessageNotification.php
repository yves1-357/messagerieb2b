<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\User;
use App\Mail\NewMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class SendMessageNotification implements ShouldQueue
{
    use Queueable;

    public $message;
    public $recipients;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $message, Collection $recipients)
    {
        $this->message = $message;
        $this->recipients = $recipients;
    }

    /**
     * Execute job.
     */
    public function handle(): void
    {
        try {
            // Charger explicitement les relations nécessaires
            $this->message->load('user', 'conversation');

            Log::info('SMTP Configuration:', [
                'MAIL_MAILER' => env('MAIL_MAILER'),
                'MAIL_HOST' => env('MAIL_HOST'),
                'MAIL_PORT' => env('MAIL_PORT'),
                'MAIL_USERNAME' => env('MAIL_USERNAME'),
                'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
            ]);

            Log::info('Starting email notification job', [
                'message_id' => $this->message->id,
                'recipients_count' => $this->recipients->count()
            ]);

            foreach ($this->recipients as $recipient) {
                if (is_array($recipient)) {
                    Log::warning("Recipient is an array instead of a User object", [
                        'recipient' => $recipient
                    ]);
                    continue;
                }

                if ($recipient instanceof User && $recipient->email && str_ends_with($recipient->email, '@gmail.com')) {
                    if ($recipient->id !== $this->message->user_id) {
                        try {
                            Mail::to($recipient->email)->send(
                                new NewMessageNotification($this->message, $recipient)
                            );

                            Log::info("Email notification sent successfully", [
                                'recipient' => $recipient->email,
                                'message_id' => $this->message->id
                            ]);
                        } catch (\Exception $e) {
                            Log::error("Failed to send email to {$recipient->email}", [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            throw $e; // Renvoie pour que le job soit marqué comme failed
                        }
                    } else {
                        Log::info("Skipped sending email to message sender: {$recipient->email}");
                    }
                } else {
                    Log::info("Skipped recipient (not Gmail or invalid User)", [
                        'recipient_type' => get_class($recipient),
                        'email' => $recipient->email ?? 'no email'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send message notification', [
                'error_message' => $e->getMessage(),
                'message_id' => $this->message->id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * The nombre de fois que le job peut être tenté.
     */
    public $tries = 3;

    /**
     * Calcula le nombre de secondes à attendre avant de réessayer le job.
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }
}
