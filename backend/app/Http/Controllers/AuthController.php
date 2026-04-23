<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. REGISZTRÁCIÓ
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed', // A Vue-ból kell egy 'password_confirmation' mező is!
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user' // Alapértelmezetten mindenki sima vásárló
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Sikeres regisztráció!',
            'access_token' => $token,
            'user' => $user
        ], 201);
    }

    // 2. BEJELENTKEZÉS
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Ellenőrizzük, hogy létezik-e a user és jó-e a jelszó
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Hibás e-mail cím vagy jelszó!'], 401);
        }

        // Töröljük a korábbi tokenjeit (hogy ne halmozódjanak), majd adunk egy újat
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Sikeres bejelentkezés!',
            'access_token' => $token,
            'user' => $user
        ]);
    }

    // 3. KIJELENTKEZÉS (Csak bejelentkezve hívható)
    public function logout(Request $request)
    {
        // Töröljük az aktuális tokent
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sikeres kijelentkezés!']);
    }

    // 4. BEJELENTKEZETT FELHASZNÁLÓ ADATAINAK LEKÉRÉSE
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
