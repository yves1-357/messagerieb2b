<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template qui se partage au front.
     *
     * @var string
     */
    protected $rootView = 'app'; // definit blade  qui charge l'appli vue

    /**
     * Determine la version courante.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
        // gere cache (css/js)
        // force rechargement si fichier changent
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? $request->user()->only([
                    'id', 'name', 'email', 'username', 'status', 'last_seen_at', 'created_at'
                ]) : null,
            ],
        ];
    }
}
