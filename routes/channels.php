<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Canal pour les conversations privées
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // Vérifier que l'utilisateur fait partie de cette conversation
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    return $conversation->users()->where('user_id', $user->id)->exists();
});

// Canal pour les notifications utilisateur
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Canal de présence pour voir qui est en ligne
Broadcast::channel('chat-presence', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar_color' => '#8B5CF6' // Couleur par défaut
    ];
});
