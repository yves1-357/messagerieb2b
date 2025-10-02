<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Mail\NewNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNotificationEmail implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $notification;

    /**
     *
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Execute.
     */
    public function handle(): void
    {
        $user = $this->notification->user;

        // Vérifier si l'utilisateur veut des notifications par email
        if ($user->email_notifications) {
            Mail::to($user->email)->send(new NewNotification($this->notification));

            // Marquer comme envoyé
            $this->notification->update(['emailed' => true]);
        }
    }
}
