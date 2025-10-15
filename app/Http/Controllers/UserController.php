<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users for search.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $currentUser = Auth::user();

        $query = User::select('id', 'name', 'username', 'avatar', 'is_online')
            ->where('id', '!=', $currentUser->id);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->limit(20)->get();

        return response()->json($users);
    }

    /**
     * Search users by username specifically.
     */
    public function search(Request $request)
    {
        $username = $request->input('username');
        $currentUser = Auth::user();

        if (!$username) {
            return response()->json([]);
        }

        $users = User::select('id', 'name', 'username', 'avatar', 'is_online')
            ->where('username', 'like', "%{$username}%")
            ->where('id', '!=', $currentUser->id)
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}
