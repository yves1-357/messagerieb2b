<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Route de healthcheck pour Railway
Route::get('/health', function (): JsonResponse {
    return response()->json(['status' => 'ok'], 200);
});

// Route de diagnostic database
Route::get('/debug-db', function () {
    try {
        $tables = DB::select('SHOW TABLES');
        $sessionTable = DB::select("SHOW TABLES LIKE 'sessions'");

        return response()->json([
            'database_connected' => true,
            'total_tables' => count($tables),
            'sessions_table_exists' => count($sessionTable) > 0,
            'session_driver' => config('session.driver'),
            'app_env' => config('app.env')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'database_connected' => false,
            'error' => $e->getMessage()
        ]);
    }
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

// Route de test pour diagnostiquer les problèmes Google Auth
Route::get('/debug-google-auth', function () {
    $info = [
        'google_client_id' => config('services.google.client_id') ? 'CONFIGURÉ' : 'MANQUANT',
        'google_client_secret' => config('services.google.client_secret') ? 'CONFIGURÉ' : 'MANQUANT',
        'google_redirect' => config('services.google.redirect'),
        'app_url' => config('app.url'),
        'current_url' => request()->url(),
        'session_status' => [
            'session_id' => session()->getId(),
            'auth_check' => Auth::check(),
            'manual_auth' => session('auth_user_id'),
        ],
        'recent_logs' => 'Vérifiez les logs Railway pour les détails'
    ];

    return '<h1>Debug Google Auth</h1><pre>' . json_encode($info, JSON_PRETTY_PRINT) . '</pre>
            <p><a href="/auth/google">Tester Google Auth</a></p>';
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/chat', function (){
    Log::info('Accessing /chat route. Auth check: ' . (Auth::check() ? 'true' : 'false'));
    Log::info('User ID: ' . (Auth::id() ?? 'null'));
    Log::info('Manual session user: ' . session('auth_user_id', 'none'));

    // Test: Vérifier la session manuelle si Laravel Auth échoue
    if (!Auth::check() && session('auth_user_id')) {
        Log::info('Using manual session auth');
        $userId = session('auth_user_id');
        $userName = session('auth_user_name');

        return Inertia::render('Chat', [
            'user' => [
                'id' => $userId,
                'name' => $userName
            ],
            'auth_method' => 'manual_session'
        ]);
    }

    if (!Auth::check()) {
        Log::warning('User not authenticated, redirecting to home');
        return redirect('/')->with('error', 'Vous devez être connecté pour accéder au chat');
    }

    return Inertia::render('Chat');
})->name('chat');// Route de test pour Google Auth (sans middleware)
Route::get('/auth-success', function () {
    return Inertia::render('Chat');
})->name('auth.success');

// Route de test auth avec vérification
Route::get('/test-auth', function () {
    $isAuth = Auth::check();
    $user = Auth::user();

    // Vérifier aussi la session manuelle
    $manualUserId = session('auth_user_id');
    $manualUserName = session('auth_user_name');

    return response()->json([
        'authenticated' => $isAuth,
        'user_id' => $user ? $user->id : null,
        'user_name' => $user ? $user->name : null,
        'session_id' => session()->getId(),
        'manual_user_id' => $manualUserId,
        'manual_user_name' => $manualUserName,
        'session_driver' => config('session.driver'),
        'message' => $isAuth ? 'Utilisateur connecté via Laravel Auth' : 'Utilisateur non connecté'
    ]);
});// Route de diagnostic sessions
Route::get('/debug-sessions', function () {
    try {
        $sessionDriver = config('session.driver');
        $dbConnection = DB::connection()->getDatabaseName();

        // Vérifier si la table sessions existe
        $tablesExist = DB::select("SHOW TABLES LIKE 'sessions'");

        // Compter les sessions en DB
        $sessionCount = 0;
        $currentSessionInDb = null;
        if (!empty($tablesExist)) {
            $sessionCount = DB::table('sessions')->count();
            $currentSessionInDb = DB::table('sessions')
                ->where('id', session()->getId())
                ->first();
        }

        $info = [
            'session_driver' => $sessionDriver,
            'database_name' => $dbConnection,
            'sessions_table_exists' => !empty($tablesExist),
            'session_id' => session()->getId(),
            'total_sessions_in_db' => $sessionCount,
            'current_session_in_db' => $currentSessionInDb ? 'EXISTS' : 'NOT_FOUND',
            'session_config' => [
                'lifetime' => config('session.lifetime'),
                'path' => config('session.path'),
                'domain' => config('session.domain'),
                'secure' => config('session.secure'),
                'http_only' => config('session.http_only'),
                'same_site' => config('session.same_site')
            ],
            'cookies' => request()->cookies->all()
        ];

        // Test d'écriture de session
        session(['test_key' => 'test_value_' . time()]);
        session()->save();
        $info['session_test_value'] = session('test_key');
        $info['all_session_data'] = session()->all();

        return '<h1>Debug Sessions</h1><pre>' . json_encode($info, JSON_PRETTY_PRINT) . '</pre>';
    } catch (\Exception $e) {
        return '<h1>Erreur Sessions</h1><p>' . $e->getMessage() . '</p>';
    }
});

// Route pour créer la table sessions manuellement
Route::get('/create-sessions-table', function () {
    try {
        // Vérifier si la table existe déjà
        $exists = DB::select("SHOW TABLES LIKE 'sessions'");
        if (!empty($exists)) {
            return '<h1>Table sessions existe déjà</h1>';
        }

        // Créer la table sessions
        DB::statement('
            CREATE TABLE sessions (
                id VARCHAR(255) NOT NULL,
                user_id BIGINT UNSIGNED NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                payload LONGTEXT NOT NULL,
                last_activity INT NOT NULL,
                PRIMARY KEY (id),
                INDEX sessions_user_id_index (user_id),
                INDEX sessions_last_activity_index (last_activity)
            )
        ');

        return '<h1>✅ Table sessions créée avec succès !</h1>
                <p><a href="/debug-sessions">Vérifier les sessions</a></p>
                <p><a href="/debug-db">Vérifier la base de données</a></p>';

    } catch (\Exception $e) {
        return '<h1>❌ Erreur lors de la création</h1><p>' . $e->getMessage() . '</p>';
    }
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route principale - Page d'authentification
Route::get('/', function () {
    return Inertia::render('Authpage');
})->name('home');

// Route pour tester la persistance de session
Route::get('/test-session-persistence', function () {
    $step = request()->get('step', '1');

    if ($step == '1') {
        // Étape 1: Sauvegarder dans la session
        session(['persistence_test' => 'session_saved_at_' . time()]);
        session(['manual_auth_test' => 'user_123']);
        session()->save();

        return '<h1>Étape 1: Session sauvegardée</h1>
                <p>Session ID: ' . session()->getId() . '</p>
                <p>Valeur sauvée: ' . session('persistence_test') . '</p>
                <p><a href="/test-session-persistence?step=2">Tester la persistance →</a></p>';
    }

    if ($step == '2') {
        // Étape 2: Vérifier si la session persiste
        $persistenceTest = session('persistence_test');
        $manualAuthTest = session('manual_auth_test');

        return '<h1>Étape 2: Test de persistance</h1>
                <p>Session ID: ' . session()->getId() . '</p>
                <p>Valeur récupérée: ' . ($persistenceTest ?? 'NULL') . '</p>
                <p>Auth manuelle: ' . ($manualAuthTest ?? 'NULL') . '</p>
                <p>Persistance: ' . ($persistenceTest ? '✅ Fonctionne' : '❌ Échoue') . '</p>
                <p><a href="/test-session-persistence?step=1">Recommencer</a></p>';
    }
});

// Route catch-all pour SPA - DOIT ÊTRE EN DERNIER
Route::get('/{any}', function () {
    return Inertia::render('Authpage');
})->where('any', '^(?!debug-|create-sessions-table|auth-success|test-session-persistence).*$');

