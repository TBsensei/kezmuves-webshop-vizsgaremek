<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Nyilvános (Public) Végpontok - Bárki elérheti
|--------------------------------------------------------------------------
*/
// Autentikáció
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Termékek lekérése (vásárlóknak)
Route::get('/products', [ProductController::class, 'index']);

// Rendelés leadása (Most már lehet, hogy be van jelentkezve a vevő, erről majd a Vue gondoskodik)
Route::post('/orders', [OrderController::class, 'store']);


/*
|--------------------------------------------------------------------------
| Védett (Protected) Végpontok - Csak bejelentkezve (Tokennel)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Felhasználó adatai és kijelentkezés
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);

    // --- ADMIN VÉGPONTOK ---
    // (A Vue frontend is le fogja tiltani az oldalt, de a biztonság kedvéért itt is jó tudni róluk)
    Route::get('/orders', [OrderController::class, 'index']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});
