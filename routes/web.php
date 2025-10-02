<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Http\JsonResponse;

// Route de healthcheck pour Railway
Route::get('/health', function (): JsonResponse {
    return response()->json(['status' => 'ok'], 200);
});

// Route de test simple
Route::get('/test', function () {
    return '<h1>Laravel fonctionne !</h1><p>Version: ' . app()->version() . '</p>';
});

// Route de test Inertia
Route::get('/test-inertia', function () {
    return Inertia::render('TestPage', [
        'message' => 'Inertia fonctionne !',
        'timestamp' => now()->toISOString()
    ]);
});

// Route de test ultra-simple
Route::get('/simple-test', function () {
    return Inertia::render('SimpleTest');
});

// Route de test CDN
Route::get('/test-cdn', function () {
    return response()->file(public_path('test-cdn.html'));
});

Route::post('/register', [RegisteredUserController::class, 'register']);

// Routes explicites pour login et register
Route::post('/login', [RegisteredUserController::class, 'login'])->name('login');

Route::get('/register', function () {
    return Inertia::render('Authpage');
})->name('register');

//route pour authentification google
Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/chat', function (){
    return Inertia::render('Chat');
})->middleware(['auth'])->name('chat');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});
Route::get('/', function () {
    return '<h1>🚀 Laravel fonctionne sur Railway !</h1>
            <p><strong>Timestamp:</strong> ' . now() . '</p>
            <p><strong>Environment:</strong> ' . app()->environment() . '</p>
            <div style="margin: 20px 0;">
                <h3>Tests disponibles :</h3>
                <p><a href="/test">✅ Test PHP Simple</a></p>
                <p><a href="/test-cdn">🧪 Test Vue.js CDN</a></p>
                <p><a href="/simple-test">❌ Test Inertia Simple</a></p>
                <p><a href="/test-inertia">❌ Test Inertia Complet</a></p>
            </div>';
})->name('home');




Route::get('/{any}', function () {
    return Inertia::render('Authpage');
})->where('any', '.*');



