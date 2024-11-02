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
       /* 
        $boardId = $request->route('boardId');
        $user = Auth::user();
        $board = Board::find($boardId);
        //$board =$user->boards()->find($boardId);
        if (!$board) {
            return response()->json(['error' => 'Board not found'.$boardId], 404);
        }
        
        // Проверяем, имеет ли пользователь доступ к доске
        $access = $board->users()->where('user_id', $user->id)->first();
        Log::info('Checking access for user: ' . $user->id . ' on board: '.$boardId.' Access: ' . $access);
        if (!$access) {
            return response()->json(['error' => 'Access denied'], 403);
        }
      
        // Передаем роль пользователя в запрос для использования в контроллере
        $request->merge(['user_role' => $access->pivot->role]);
        Log::info('user_role: ' . $user->id . ' on board: ' . $access->pivot->role);
        return $next($request);
        */
            $boardId = $request->route('boardId');
        $user = Auth::user();
        $board = Board::find($boardId);

        if (!$board) {
            return response()->json(['error' => 'Board not found' . $boardId], 404);
        }

        // Проверяем, имеет ли пользователь доступ к доске
        $access = $board->users()->where('user_id', $user->id)->first();
        if (!$access) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Получаем роль пользователя и передаем ее в запрос для использования в контроллере
        $userRole = $access->pivot->role;
        $request->merge(['user_role' => $userRole]);

        // Блокируем доступ для роли 'viewer' к маршрутам с методами PUT, PATCH, DELETE
        if ($userRole === 'viewer' && in_array($request->method(), ['POST','PUT', 'PATCH', 'DELETE'])) {
            return response()->json(['error' => 'Insufficient permissions to modify or delete'], 403);
        }
        if ($userRole === 'editor' && in_array($request->method(), ['DELETE'])) {
            return response()->json(['error' => 'Insufficient permissions to delete'], 403);
        }

        return $next($request);
    }
}
