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
                // Pour les conversations de groupe
                if ($conversation->is_group) {
                    return [
                        'id' => $conversation->id,
                        'name' => $conversation->name_group,
                        'is_group' => true,
                        'participants_count' => $conversation->users->count(),
                        'avatar_color' => '#10B981', // Couleur verte pour les groupes
                        'last_message' => $conversation->lastMessage?->content ?? '',
                        'last_message_time' => $conversation->last_message_at?->diffForHumans() ?? '',
                        'formatted_time' => $conversation->last_message_at ? $conversation->last_message_at->format('H:i') : '',
                        'unread_count' => 0, // à implémenter le comptage des non-lus
                        'users' => $conversation->users->map(function ($participant) {
                            return [
                                'id' => $participant->id,
                                'name' => $participant->name,
                                'email' => $participant->email,
                                'avatar' => $participant->avatar,
                                'avatar_color' => $participant->avatar_color ?? '#8B5CF6',
                                'is_online' => $participant->is_online ?? false,
                                'last_seen_at' => $participant->last_seen_at,
                            ];
                        }),
                    ];
                }

                // Pour les conversations privées
                $otherParticipant = $conversation->getOtherParticipant($user->id);

                return [
                    'id' => $conversation->id,
                    'name' => $otherParticipant?->name ?? 'Utilisateur supprimé',
                    'is_group' => false,
                    'avatar_color' => $otherParticipant?->avatar_color ?? '#8B5CF6',
                    'last_message' => $conversation->lastMessage?->content ?? '',
                    'last_message_time' => $conversation->last_message_at?->diffForHumans() ?? '',
                    'formatted_time' => $conversation->last_message_at ? $conversation->last_message_at->format('H:i') : '',
                    'is_online' => $otherParticipant?->isOnline() ?? false,
                    'unread_count' => 0, // à implémenter le comptage des non-lus
                    'other_user' => $otherParticipant ? [
                        'id' => $otherParticipant->id,
                        'name' => $otherParticipant->name,
                        'username' => $otherParticipant->username,
                        'email' => $otherParticipant->email,
                        'status' => $otherParticipant->status,
                        'last_seen_at' => $otherParticipant->last_seen_at,
                        'avatar_color' => $otherParticipant->avatar_color ?? '#8B5CF6',
                        'is_online' => $otherParticipant->is_online ?? false,
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
            'user_id' => 'required|integer|exists:users,id'
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
            ->with(['users', 'lastMessage'])
            ->first();

        if ($existingConversation) {
            // Obtenir l'autre participant (pas l'utilisateur actuel)
            $otherUser = $existingConversation->users->where('id', '!=', $currentUser->id)->first();

            return response()->json([
                'id' => $existingConversation->id,
                'name' => $otherUser->name,
                'type' => $existingConversation->type,
                'avatar_color' => $otherUser->avatar_color ?? '#8B5CF6',
                'last_message' => $existingConversation->lastMessage?->content ?? '',
                'last_message_time' => $existingConversation->last_message_at?->diffForHumans() ?? 'now',
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
                'users' => $existingConversation->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'status' => $user->status,
                        'last_seen_at' => $user->last_seen_at,
                        'avatar_color' => $user->avatar_color ?? '#8B5CF6',
                    ];
                })->toArray()
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

            // Obtenir l'autre participant (pas l'utilisateur actuel)
            $otherUser = $conversation->users->where('id', '!=', $currentUser->id)->first();

            return response()->json([
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
                'users' => $conversation->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'status' => $user->status,
                        'last_seen_at' => $user->last_seen_at,
                        'avatar_color' => $user->avatar_color ?? '#8B5CF6',
                    ];
                })->toArray()
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create conversation', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Créer une conversation de groupe
     */
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id'
        ]);

        $user = Auth::user();

        DB::beginTransaction();
        try {
            // Créer la conversation de groupe
            $conversation = Conversation::create([
                'name_group' => $request->name,
                'is_group' => true,
            ]);

            // Ajouter l'utilisateur actuel et tous les participants
            $participantIds = array_unique(array_merge([$user->id], $request->participants));
            $conversation->users()->attach($participantIds);

            // Charger la conversation avec les relations nécessaires
            $conversation->load(['users' => function ($query) use ($user) {
                $query->where('users.id', '!=', $user->id);
            }, 'messages']);

            // Formater la réponse
            $otherUsers = $conversation->users;

            $conversationData = [
                'id' => $conversation->id,
                'name' => $conversation->name_group,
                'is_group' => true,
                'participants_count' => count($participantIds),
                'last_message' => null,
                'last_message_time' => null,
                'formatted_time' => '',
                'unread_count' => 0,
                'other_user' => null, // Pas d'autre utilisateur unique pour un groupe
                'users' => $otherUsers->map(function ($participant) {
                    return [
                        'id' => $participant->id,
                        'name' => $participant->name,
                        'email' => $participant->email,
                        'avatar' => $participant->avatar,
                        'avatar_color' => $participant->avatar_color ?? '#8B5CF6',
                        'is_online' => $participant->is_online ?? false,
                        'last_seen_at' => $participant->last_seen_at,
                    ];
                }),
                'created_at' => $conversation->created_at,
                'updated_at' => $conversation->updated_at,
            ];

            DB::commit();

            return response()->json($conversationData, 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Failed to create group conversation',
                'message' => $e->getMessage()
            ], 500);
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
