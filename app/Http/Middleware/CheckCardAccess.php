<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Card;
use App\Models\Board;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckCardAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {

        /*
        $cardId = $request->route('cardId');
        $boarId = $request->route('boardId');
        $user = Auth::user();
        Log::info('Checking card access for user: ' . $user->id . ' on card: ' . $cardId);
        $card = Card::find($cardId);
        if (!$card) {
            return response()->json(['error' => 'Card not found'], 404);
        }
        $board = Board::find($boarId);
        if (!$board) {
            return response()->json(['error' => 'Board not found'], 404);
        }

        // Проверяем, имеет ли пользователь доступ к доске
        $cardAccess = $card->users()->where('user_id', $user->id)->first();
        Log::info('Checking access for user: ' . $user->id . ' on card: ' . $cardAccess);
        if (!$cardAccess) {
            $boardAccess = $board->users()->where('user_id', $user->id)->first();
            Log::info('Checking access for user: ' . $user->id . ' on card: ' . $boardAccess);
            if (!$boardAccess) 
            {
                return response()->json(['error' => 'Access denied'], 403);
            }
            // Передаем роль пользователя в запрос для использования в контроллере
            $request->merge(['user_role' => $boardAccess->pivot->role]);
            Log::info('user_role: ' . $user->id . ' on board: ' . $boardAccess->pivot->role);
            return $next($request);
            
        }
        $request->merge(['user_role' => $cardAccess->pivot->role]);
        Log::info('user_role: ' . $user->id . ' on card: ' . $cardAccess->pivot->role);
        return $next($request);

        */

        $cardId = $request->route('cardId');
        $boardId = $request->route('boardId');
        $user = Auth::user();
        
        $card = Card::find($cardId);
        if (!$card) {
            return response()->json(['error' => 'Card not found'], 404);
        }
        
        $board = Board::find($boardId);
        if (!$board) {
            return response()->json(['error' => 'Board not found'], 404);
        }
    
        // Проверяем, имеет ли пользователь доступ к карточке
        $cardAccess = $card->users()->where('user_id', $user->id)->first();
        if (!$cardAccess) {
            $boardAccess = $board->users()->where('user_id', $user->id)->first();
            if (!$boardAccess) {
                return response()->json(['error' => 'Access denied'], 403);
            }
            $userRole = $boardAccess->pivot->role;
        } else {
            $userRole = $cardAccess->pivot->role;
        }
    
        // Блокируем доступ для роли 'viewer' к маршрутам с методами PUT, PATCH, DELETE
        if ($userRole === 'viewer' && in_array($request->method(), ['POST','PUT', 'PATCH', 'DELETE'])) {
            return response()->json(['error' => 'Insufficient permissions to modify or delete'], 403);
        }
        if ($userRole === 'editor' && in_array($request->method(), ['DELETE'])) {
            return response()->json(['error' => 'Insufficient permissions to delete'], 403);
        }
    
        $request->merge(['user_role' => $userRole]);
        return $next($request);
      
    }
}
