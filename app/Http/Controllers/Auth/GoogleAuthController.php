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
        Log::info('=== CALLBACK GOOGLE APPELÉ ===');
        Log::info('URL complète: ' . request()->fullUrl());
        Log::info('Paramètres GET: ', request()->query());

        try {
            Log::info('Tentative de récupération user Google...');
            $googleUser = Socialite::driver('google')->user();

            Log::info('Google user récupéré avec succès: ', [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'id' => $googleUser->id
            ]);

            // Pour test: juste retourner une confirmation
            return '<h1>✅ Callback Google Success!</h1>
                    <p>User: ' . $googleUser->name . '</p>
                    <p>Email: ' . $googleUser->email . '</p>
                    <p>Google ID: ' . $googleUser->id . '</p>
                    <p><a href="/chat">Aller au chat (test)</a></p>';

        } catch (\Exception $e) {
            Log::error('❌ ERREUR dans callback Google: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return '<h1>❌ Erreur Google Callback</h1>
                    <p>Erreur: ' . $e->getMessage() . '</p>
                    <p><a href="/">Retour accueil</a></p>';
        }
    }
}
