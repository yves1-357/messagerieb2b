<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Dotenv\Exception\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

     public function register(Request $request)
    {
        Log::info('Début méthode register', ['data' => $request->all()]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'min:5'],
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            event(new Registered($user));

            Auth::login($user);

            // retourne reponse JSON
            return response()->json(['success' => true]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // autres erreurs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //connexion
    public function login (Request $request){
        $validated = $request->validate([
           'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

         $user = User::where('email', $validated['email'])->first();

         if (! $user || ! Hash::check($validated['password'], $user->password)) {
           return response()->json([
            'message' => 'Identifiants incorrects.'
           ], 422);

        }



        Auth::login($user, $request->boolean('remember'));

         $request->session()->regenerate();
        return response()->json([
            'message' => 'Connexion réussie',
            'user'    => ['id' => $user->id, 'name' => $user->name]
        ]);
    }
    }

