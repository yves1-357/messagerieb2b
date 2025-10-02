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

            Log::info('Google user data: ', [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'id' => $googleUser->id
            ]);

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
                Log::info('New user created: ' . $user->id);
            } else {
                // Mettre à jour le google_id si nécessaire
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
                Log::info('Existing user found: ' . $user->id);
            }

            // Connecter l'utilisateur avec remember
            Auth::login($user, true);

            // Forcer la sauvegarde de la session SANS régénération pour test
            request()->session()->save();

            // Debug: vérifier que l'utilisateur est bien connecté
            Log::info('Auth check after login: ' . (Auth::check() ? 'true' : 'false'));
            Log::info('Authenticated user ID: ' . (Auth::id() ?? 'null'));
            Log::info('Session ID after login: ' . request()->session()->getId());

            // Test: Sauvegarder manuellement l'auth dans la session
            session(['auth_user_id' => $user->id]);
            session(['auth_user_name' => $user->name]);
            session()->save();

            Log::info('Manual session saved with user: ' . $user->id);

            Log::info('User logged in successfully, redirecting to chat');

            // Pause pour s'assurer que la session est sauvegardée
            sleep(1);

            // Redirection directe vers chat
            return redirect('/chat');

        } catch (\Exception $e) {
            Log::error('Google Auth Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // En cas d'erreur, rediriger vers login avec un message d'erreur
            return redirect('/')->with('error', 'Erreur lors de la connexion avec Google: ' . $e->getMessage());
        }
    }
}
