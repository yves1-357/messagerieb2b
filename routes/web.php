<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

// Route de healthcheck pour Railway
Route::get('/health', function (): JsonResponse {
    return response()->json(['status' => 'ok'], 200);
});

// Routes d'authentification
Route::post('/register', [RegisteredUserController::class, 'register']);
Route::post('/login', [RegisteredUserController::class, 'login'])->name('login');

Route::get('/register', function () {
    return Inertia::render('Authpage');
})->name('register');

// Routes d'authentification Google
Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route Chat principale
Route::get('/chat', function () {
    // Vérifier l'authentification Laravel standard
    if (!Auth::check()) {
        // Fallback : vérifier la session manuelle (pour compatibilité Railway)
        if (session('auth_user_id')) {
            $userId = session('auth_user_id');
            $userName = session('auth_user_name');

            return Inertia::render('Chat', [
                'user' => [
                    'id' => $userId,
                    'name' => $userName
                ]
            ]);
        }

        return redirect('/')->with('error', 'Vous devez être connecté pour accéder au chat');
    }

    return Inertia::render('Chat');
})->name('chat');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour les conversations
    Route::get('/api/conversations', [ConversationController::class, 'index']);
    Route::post('/api/conversations', [ConversationController::class, 'store']);
    Route::get('/api/conversations/{conversation}', [ConversationController::class, 'show']);

    // Routes pour les messages
    Route::get('/api/conversations/{conversation}/messages', [MessageController::class, 'index']);
    Route::post('/api/conversations/{conversation}/messages', [MessageController::class, 'store']);

    // Routes pour les utilisateurs
    Route::get('/api/users/search', [UserController::class, 'search']);
    Route::get('/api/users', [UserController::class, 'index']);
    Route::get('/api/users/available', [UserController::class, 'getAvailableUsers']);

    // Routes pour le système username
    Route::patch('/api/user/username', [UserController::class, 'updateUsername']);
    Route::post('/api/auth/logout', [UserController::class, 'logout']);
    Route::delete('/api/user/account', [UserController::class, 'deleteAccount']);
});

// Route principale - Page d'authentification
Route::get('/', function () {
    return Inertia::render('Authpage');
})->name('home');

// Route catch-all pour SPA
Route::get('/{any}', function () {
    return Inertia::render('Authpage');
})->where('any', '.*');

