<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $conversations = $user->conversations()
            ->with(['users', 'latestMessage'])
            ->get();

        return response()->json($conversations);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:private,group',
            'name' => 'nullable|string|max:255',
            'user_ids' => 'required|array',
        ]);

        $user = Auth::user();

        $conversation = Conversation::create([
            'name' => $request->name,
            'type' => $request->type,
            'created_by' => $user->id,
        ]);

        // Ajouter les utilisateurs
        $conversation->users()->attach($user->id, ['role' => 'admin']);
        foreach ($request->user_ids as $userId) {
            $conversation->users()->attach($userId, ['role' => 'member']);
        }

        return response()->json($conversation->load('users'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();

        // Vérifier l'accès
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        $conversation->load(['users', 'messages.user']);

        return response()->json($conversation);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
