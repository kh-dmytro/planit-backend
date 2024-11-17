<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Добавлен импорт для JsonResponse
use Closure; // Используем глобальный \Closure, а не App\Http\Middleware\Closure
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    /*
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
        */

    /*
        protected function unauthenticated($request, array $guards)
        {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        */

    public function handle($request, Closure $next, ...$guards)
    {
        Log::info('Sanctum Middleware: Checking user auth');
        try {
            $this->authenticate($request, $guards);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            Log::info('Sanctum Middleware: Unauthorized access');
            return new JsonResponse([
                'error' => 'Unauthorized',
                'message' => 'Invalid or missing token'
            ], 401);
        }
        return $next($request);
    }
}
