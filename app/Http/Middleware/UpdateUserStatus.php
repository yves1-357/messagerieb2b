<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Mettre à jour le statut et last_seen_at de l'utilisateur connecté
            Auth::user()->update([
                'status' => 'online',
                'last_seen_at' => now()
            ]);
        }

        $response = $next($request);

        // Optionnel: Mettre le statut offline après la réponse (pour les déconnexions)
        if (Auth::check() && $request->is('logout')) {
            Auth::user()->update(['status' => 'offline']);
        }

        return $response;
    }
}
