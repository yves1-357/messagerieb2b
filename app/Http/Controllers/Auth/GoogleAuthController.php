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

            Log::info('Google Auth: Utilisateur connecté', [
                'name' => $googleUser->name,
                'email' => $googleUser->email
            ]);

            // Trouver ou créer l'utilisateur
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(rand(1,10000)),
                    'email_verified_at' => now(),
                ]);
                Log::info('Nouvel utilisateur créé: ' . $user->email);
            } else {
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
                Log::info('Utilisateur existant connecté: ' . $user->email);
            }

            // Authentification Laravel
            Auth::login($user, true);
            request()->session()->save();

            // Backup session manuelle pour Railway
            session(['auth_user_id' => $user->id]);
            session(['auth_user_name' => $user->name]);
            session()->save();

            return redirect('/chat');

        } catch (\Exception $e) {
            Log::error('ERREUR dans callback Google: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return '<h1>Erreur Google Callback</h1>
                    <p>Erreur: ' . $e->getMessage() . '</p>
                    <p><a href="/">Retour accueil</a></p>';
        }
    }
}
