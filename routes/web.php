<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Log;

Route::post('/register', [RegisteredUserController::class, 'register']);

// Routes explicites pour login et register
Route::post('/login', [RegisteredUserController::class, 'login'])->name('login');

Route::get('/register', function () {
    return Inertia::render('Authpage');
})->name('register');


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
    return Inertia::render('Authpage');
})->name('home');




Route::get('/{any}', function () {
    return Inertia::render('Authpage');
})->where('any', '.*');



