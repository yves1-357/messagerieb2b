<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirection a Google pour  authentication
     */
    public function redirectToGoogle()
    {
        Log::info('redirectToGoogle method called');
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Error in redirectToGoogle: ' . $e->getMessage());
            return redirect('/')->with('error', 'Erreur lors de la redirection vers Google.');
        }
    }

    /**
     * retour de Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Trouver ou créer l'utilisateur
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Créer un nouvel utilisateur
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(rand(1,10000)), // Mot de passe aléatoire
                    'email_verified_at' => now(),
                ]);
            }

            // Connecter l'utilisateur
            Auth::login($user);

            // Rediriger vers la page de chat
            return redirect('/chat');

        } catch (\Exception $e) {
            // En cas d'erreur, rediriger vers login avec un message d'erreur
            return redirect('/login')->with('error', 'Erreur lors de la connexion avec Google.');
        }
    }
}
