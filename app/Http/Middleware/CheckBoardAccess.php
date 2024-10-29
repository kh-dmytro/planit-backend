<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Board;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckBoardAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        
        $boardId = $request->route('boardId');
        $user = Auth::user();
        Log::info('Checking board access for user: ' . $user->id . ' on board: ' . $boardId);
        $board = Board::find($boardId);
        if (!$board) {
            return response()->json(['error' => 'Board not found'], 404);
        }

        // Проверяем, имеет ли пользователь доступ к доске
        $access = $board->users()->where('user_id', $user->id)->first();
        Log::info('Checking access for user: ' . $user->id . ' on board: ' . $access);
        if (!$access) {
            return response()->json(['error' => 'Access denied'], 403);
        }
      
        // Передаем роль пользователя в запрос для использования в контроллере
        $request->merge(['user_role' => $access->pivot->role]);
        Log::info('user_role: ' . $user->id . ' on board: ' . $access->pivot->role);
        return $next($request);
    }
}
