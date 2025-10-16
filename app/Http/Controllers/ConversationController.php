<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    /**
     * Obtenir toutes les conversations de l'utilisateur connecté
     */
    public function index()
    {
        $user = Auth::user();

        $conversations = $user->conversations()
            ->with(['users', 'lastMessage.user'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($user) {
                $otherParticipant = $conversation->getOtherParticipant($user->id);

                return [
                    'id' => $conversation->id,
                    'name' => $conversation->type === 'private'
                        ? $otherParticipant?->name
                        : $conversation->name,
                    'type' => $conversation->type,
                    'avatar_color' => $otherParticipant?->avatar_color ?? '#8B5CF6',
                    'last_message' => $conversation->lastMessage?->content ?? '',
                    'last_message_time' => $conversation->last_message_at?->diffForHumans() ?? '',
                    'is_online' => $otherParticipant?->isOnline() ?? false,
                    'unread_count' => 0, // à implémenter le comptage des non-lus
                    'participant' => $otherParticipant ? [
                        'id' => $otherParticipant->id,
                        'name' => $otherParticipant->name,
                        'username' => $otherParticipant->username,
                        'email' => $otherParticipant->email,
                        'status' => $otherParticipant->status,
                        'last_seen_at' => $otherParticipant->last_seen_at,
                        'avatar_color' => $otherParticipant->avatar_color ?? '#8B5CF6',
                    ] : null,
                ];
            });

        return response()->json($conversations);
    }

    /**
     * Créer une nouvelle conversation avec un utilisateur
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $currentUser = Auth::user();
        $otherUserId = $request->user_id;

        // Vérifier qu'on ne crée pas une conversation avec soi-même
        if ($currentUser->id == $otherUserId) {
            return response()->json(['error' => 'Cannot create conversation with yourself'], 400);
        }

        // Vérifier si une conversation privée existe déjà entre ces deux utilisateurs
        $existingConversation = Conversation::where('type', 'private')
            ->whereHas('users', function ($query) use ($currentUser) {
                $query->where('user_id', $currentUser->id);
            })
            ->whereHas('users', function ($query) use ($otherUserId) {
                $query->where('user_id', $otherUserId);
            })
            ->first();

        if ($existingConversation) {
            return response()->json([
                'message' => 'Conversation already exists',
                'conversation' => $existingConversation->load(['users', 'lastMessage'])
            ]);
        }

        // Créer nouvelle conversation
        DB::beginTransaction();
        try {
            $conversation = Conversation::create([
                'type' => 'private',
                'created_by' => $currentUser->id,
                'last_message_at' => now(),
            ]);

            // Attacher les deux utilisateurs
            $conversation->users()->attach([$currentUser->id, $otherUserId], [
                'joined_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Charger les relations
            $conversation->load(['users', 'lastMessage']);

            $otherUser = User::find($otherUserId);

            return response()->json([
                'message' => 'Conversation created successfully',
                'conversation' => [
                    'id' => $conversation->id,
                    'name' => $otherUser->name,
                    'type' => $conversation->type,
                    'avatar_color' => $otherUser->avatar_color ?? '#8B5CF6',
                    'last_message' => '',
                    'last_message_time' => 'now',
                    'is_online' => $otherUser->isOnline(),
                    'unread_count' => 0,
                    'participant' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'username' => $otherUser->username,
                        'email' => $otherUser->email,
                        'status' => $otherUser->status,
                        'last_seen_at' => $otherUser->last_seen_at,
                        'avatar_color' => $otherUser->avatar_color ?? '#8B5CF6',
                    ],
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create conversation'], 500);
        }
    }

    /**
     * Obtenir les messages d'une conversation
     */
    public function show($id)
    {
        $user = Auth::user();

        $conversation = Conversation::with(['messages.user', 'users'])
            ->forUser($user->id)
            ->findOrFail($id);

        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'user_id' => $message->user_id,
                    'is_own' => $message->user_id === $user->id,
                    'timestamp' => $message->created_at,
                    'formatted_time' => $message->created_at->format('H:i'),
                    'status' => $message->read_at ? 'read' : 'sent',
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'avatar_color' => $message->user->avatar_color ?? '#8B5CF6',
                    ]
                ];
            });

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }
}
