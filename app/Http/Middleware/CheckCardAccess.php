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
        /*
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


        /*$user = Auth::user();
        $cardId = $request->route('cardId');
        Log::info('Checking card access for user: ' . $user->id . ' on card: ' . $cardId);
        // Сначала проверяем доступ на уровне карточки
        $card = Card::findOrFail($cardId);
        if (!$card) {
            return response()->json(['error' => 'card not found'], 404);
        }

        $cardAccess = $card->users()->where('user_id', $user->id)->first();

        if ($cardAccess) {
            $request->merge(['user_role' => $cardAccess->pivot->role]);
            return $next($request);
        }
        Log::info('Checking access for user: ' . $user->id . ' on card: ' . $cardAccess);
        if (!$cardAccess) {
            return response()->json(['error' => 'Access denied'], 403);
        }
        $boardId = $request->route('boardId');
        // Если доступ к карточке не найден, проверяем доступ к доске
        $boardAccess = $card->board->users()->where('user_id', $user->id)->first();

        if ($boardAccess) {
            $request->merge(['user_role' => $boardAccess->pivot->role]);
            return $next($request);
        }

         Log::info('user_role: ' . $user->id . ' on board: ' . $boardAccess->pivot->role);
        // Если нет доступа ни к карточке, ни к доске, возвращаем ошибку
        return response()->json(['error' => 'Access denied'], 403);
        */
    }
}
