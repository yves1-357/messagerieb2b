<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


//configuration routage, middleware, gestion erreurs
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php', // indication de charger web vers la
        commands: __DIR__.'/../routes/console.php', // commande artisan
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class, // gere données inertia
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class, // optimise chargement assets (css/js)
            \App\Http\Middleware\UpdateUserStatus::class, // met a jour status user
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
