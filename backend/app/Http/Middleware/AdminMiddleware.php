<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ellenőrizzük, hogy be van-e jelentkezve a felhasználó,
        // ÉS a szerepköre (role) pontosan 'admin'-e.
        if ($request->user() && $request->user()->role === 'admin') {
            // Ha minden stimmel, a kapuőr kinyitja az ajtót, és a kérés mehet tovább a Controllerbe
            return $next($request);
        }

        // 2. Ha nem admin (hanem pl. customer), akkor megtagadjuk a hozzáférést (403 Forbidden)
        return response()->json([
            'message' => 'Nincs jogosultságod ehhez a művelethez.'
        ], 403);
    }
}
