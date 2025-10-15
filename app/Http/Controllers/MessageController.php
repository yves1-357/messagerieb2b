<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display messages for a conversation.
     */
    public function index(Conversation $conversation)
    {
        $user = Auth::user();

        // Vérifier l'accès
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        // Vérifier l'accès
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string',
            'type' => 'in:text,image,file',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => $request->input('content'),
            'type' => $request->input('type', 'text'),
        ]);

        return response()->json($message->load('user'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
