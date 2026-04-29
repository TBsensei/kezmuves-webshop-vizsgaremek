<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Új felhasználó regisztrációja.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'user' // Biztonsági okokból alapértelmezett vásárló
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success'      => true,
                'message'      => 'Sikeres regisztráció!',
                'access_token' => $token,
                'user'         => $user
            ], 201);

        } catch (\Exception $e) {
            Log::error("Regisztrációs hiba: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a regisztráció során.'
            ], 500);
        }
    }

    /**
     * Bejelentkezés és token generálása.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Jelszó és felhasználó ellenőrzése
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Hibás e-mail cím vagy jelszó!'
            ], 401);
        }

        // Korábbi tokenek takarítása (session management)
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'      => true,
            'message'      => 'Sikeres bejelentkezés!',
            'access_token' => $token,
            'user'         => $user
        ], 200);
    }

    /**
     * Kijelentkezés - aktuális token érvénytelenítése.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sikeres kijelentkezés!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a kijelentkezés során.'
            ], 500);
        }
    }

    /**
     * A bejelentkezett felhasználó adatainak lekérése.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user'    => $request->user()
        ], 200);
    }
}
