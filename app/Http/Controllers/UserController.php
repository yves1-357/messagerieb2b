<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * montre liste "user"  de recherche dans "search".
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $currentUser = Auth::user();

        $query = User::select('id', 'name', 'username', 'avatar', 'status', 'last_seen_at')
            ->where('id', '!=', $currentUser->id);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->limit(20)->get();

        return response()->json($users);
    }

    /**
     * recherche des utilisateurs par nom d'utilisateur.
     */
    public function search(Request $request)
    {
        $username = $request->input('username');
        $currentUser = Auth::user();

        if (!$username) {
            return response()->json([]);
        }

        $users = User::select('id', 'name', 'username', 'avatar', 'status', 'last_seen_at')
            ->where('username', 'like', "%{$username}%")
            ->where('id', '!=', $currentUser->id)
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    /**
     * mets a jour  user's username
     */
    public function updateUsername(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('users')->ignore($user->id),
            ],
        ], [
            'username.required' => 'Le nom d\'utilisateur est requis.',
            'username.min' => 'Le nom d\'utilisateur doit contenir au moins 3 caractères.',
            'username.max' => 'Le nom d\'utilisateur ne peut pas dépasser 30 caractères.',
            'username.regex' => 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres et underscores.',
            'username.unique' => 'Ce nom d\'utilisateur est déjà pris.',
        ]);

        $user->update([
            'username' => $request->username
        ]);

        return response()->json([
            'message' => 'Nom d\'utilisateur mis à jour avec succès.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'status' => $user->status,
                'last_seen_at' => $user->last_seen_at,
                'created_at' => $user->created_at,
            ]
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ]);
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        // Optionnel : demander confirmation avec mot de passe
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Mot de passe requis pour confirmer la suppression.',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect.'
            ], 422);
        }

        // Supprimer toutes les relations
        $user->conversations()->detach(); // Retirer des conversations
        $user->messages()->delete(); // Supprimer ses messages

        // Supprimer l'utilisateur
        $user->delete();

        // Logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Compte supprimé avec succès.'
        ]);
    }
}
