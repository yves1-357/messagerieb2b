<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    /**
     * Récupérer les messages d'une conversation
     */
    public function index(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur fait partie de cette conversation
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Access denied to this conversation'], 403);
        }

        $messagesQuery = $conversation->messages()
            ->with('user:id,name,email')
            ->orderBy('created_at', 'asc');

        // Filtrer par date si le paramètre 'since' est fourni
        if ($request->has('since')) {
            $since = $request->get('since');
            $messagesQuery->where('created_at', '>', $since);
        }

        $messages = $messagesQuery->get()
            ->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'user_id' => $message->user_id,
                    'is_own' => $message->user_id === $user->id,
                    'timestamp' => $message->created_at,
                    'formatted_time' => $message->created_at->format('H:i'),
                    'formatted_date' => $message->created_at->format('Y-m-d'),
                    'status' => $message->read_at ? 'read' : 'sent',
                    'type' => $message->type,
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'avatar_color' => '#8B5CF6', // Couleur par défaut
                    ]
                ];
            });

        return response()->json([
            'messages' => $messages,
            'conversation_id' => $conversation->id
        ]);
    }

    /**
     * Envoyer un nouveau message
     */
    public function store(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur fait partie de cette conversation
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Access denied to this conversation'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:10000',
            'type' => 'sometimes|string|in:text,image,file'
        ], [
            'content.required' => 'Le message ne peut pas être vide.',
            'content.max' => 'Le message ne peut pas dépasser 10000 caractères.',
            'type.in' => 'Type de message non supporté.'
        ]);

        DB::beginTransaction();
        try {
            // Créer le message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'content' => trim($request->input('content')),
                'type' => $request->type ?? 'text'
            ]);

            // Mettre à jour le timestamp de la conversation
            $conversation->update([
                'last_message_at' => now()
            ]);

            // Charger les relations nécessaires
            $message->load('user:id,name,email');

            // Formater la réponse
            $messageData = [
                'id' => $message->id,
                'content' => $message->content,
                'user_id' => $message->user_id,
                'conversation_id' => $message->conversation_id,
                'is_own' => true,
                'timestamp' => $message->created_at,
                'formatted_time' => $message->created_at->format('H:i'),
                'formatted_date' => $message->created_at->format('Y-m-d'),
                'status' => 'sent',
                'type' => $message->type,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'avatar_color' => '#8B5CF6', // Couleur par défaut
                ]
            ];

            DB::commit();

            Log::info('Dispatching SendMessageNotification job', [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id
            ]);

            dispatch(new \App\Jobs\SendMessageNotification($message, $conversation->users()->where('users.id', '!=', $user->id)->get()));

            return response()->json([
                'message' => $messageData,
                'success' => 'Message envoyé avec succès'
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Erreur lors de l\'envoi du message',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer les messages comme lus
     */
    public function markAsRead(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        // Vérifier l'accès
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Marquer tous les messages de cette conversation comme lus pour cet utilisateur
        $conversation->messages()
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => 'Messages marked as read']);
    }

    /**
     * Supprimer un message
     */
    public function destroy(Message $message)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur est l'auteur du message
        if ($message->user_id !== $user->id) {
            return response()->json(['error' => 'Vous ne pouvez supprimer que vos propres messages'], 403);
        }

        $message->delete();

        return response()->json(['success' => 'Message supprimé avec succès']);
    }
}
