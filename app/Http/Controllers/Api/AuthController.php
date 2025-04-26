<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    /**
     * 🔥 INSCRIPTION (register)
     */
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'pseudo' => 'required|string|max:255|unique:users,pseudo',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'required|string|email|max:255|unique:users,email',
            'birthdate' => 'required|date|before:-18 years',
            'location' => 'required|string|max:255',
            'delivery_address' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('users', 'public');
        }

        /** @var User $user */
        $user = User::create([
            'full_name' => $request->full_name,
            'pseudo' => $request->pseudo,
            'phone' => $request->phone,
            'email' => $request->email,
            'birthdate' => $request->birthdate,
            'location' => $request->location,
            'delivery_address' => $request->delivery_address,
            'photo' => $photoPath,
            'password' => Hash::make('password12345'), // 🔥 À remplacer en production
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    /**
     * 🔥 CONNEXION (login)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Identifiants invalides.'
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * 🔥 MISE À JOUR DU PROFIL (update)
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $request->validate([
            'phone' => 'nullable|string|max:20',
            'delivery_address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            Storage::disk('public')->delete($user->photo);
            $path = $request->file('photo')->store('users', 'public');
            $user->photo = $path;
        }

        $user->phone = $request->phone ?? $user->phone;
        $user->delivery_address = $request->delivery_address ?? $user->delivery_address;
        $user->save();

        return response()->json($user);
    }

    /**
     * 🔥 INFO UTILISATEUR CONNECTÉ (user)
     */
    public function user(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json($user);
    }

    /**
     * 🔥 DECONNEXION (logout)
     */
    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ]);
    }
}
