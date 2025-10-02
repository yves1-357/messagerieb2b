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

// Route de test pour Google Auth (sans middleware)
Route::get('/auth-success', function () {
    return Inertia::render('Chat');
})->name('auth.success');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route principale - Page d'authentification
Route::get('/', function () {
    return Inertia::render('Authpage');
})->name('home');

// Route catch-all pour SPA
Route::get('/{any}', function () {
    return Inertia::render('Authpage');
})->where('any', '.*');

