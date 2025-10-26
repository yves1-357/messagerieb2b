<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Jobs\SendMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendEmailNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        try {
            Log::info('Listener SendEmailNotifications triggered');
            // Charger le message avec les relations nécessaires
            $message = $event->message;
            $conversation = $event->conversation;

            // Charger les relations si elles ne sont pas déjà chargées
            $message->loadMissing('user');
            $conversation->loadMissing('users');

            // Obtenir tous les participants de la conversation sauf l'expéditeur
            $recipients = $conversation->users()
                ->where('users.id', '!=', $message->user_id)
                ->get();

            Log::info('Recipients found: ' . $recipients->count());

            if ($recipients->count() > 0) {
                Log::info('Dispatching SendMessageNotification job...');
                SendMessageNotification::dispatch($message, $recipients)
                    ->delay(now()->addSeconds(5)); // Délai de 5 secondes pour éviter le spam

                Log::info("Email notification job dispatched for message {$message->id} to {$recipients->count()} recipients");
            }

        } catch (\Exception $e) {
            Log::error('Failed to dispatch email notification: ' . $e->getMessage());
            // Ne pas faire échouer l'event si l'email ne peut pas être envoyé
        }
    }

    /**
     * Determine whether the listener should be queued.
     */
    public function shouldQueue($event): bool
    {
        // On peut ajouter des conditions ici si nécessaire
        return true;
    }
}
