<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    public function index($boardId) // Принимаем boardId как параметр
    {
        /*
        $userRole = request('user_role'); // Получаем роль, переданную из middleware

        if ($userRole !== 'viewer' && $userRole !== 'editor' && $userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
            */
        // Получаем доску, принадлежащую текущему пользователю
        $board = Auth::user()->boards()->findOrFail($boardId); // Находим доску по ID
        $cards = $board->cards; // Получаем карточки этой доски
 
        //return response()->json($cards);
        return response()->json(['cards' => $cards], 200);
    }

    public function store(Request $request, $boardId)
    {
        /*
        $userRole = request('user_role'); // Получаем роль, переданную из middleware

        if ($userRole !== 'editor' && $userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
            */
        $user = Auth::user();

        // Проверка на наличие аутентифицированного пользователя
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Проверьте, существует ли доска с указанным ID
        $board = $user->boards()->find($boardId);
        if (!$board) {
            return response()->json(['message' => 'Board not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Создаем карточку для найденной доски
        $card = $board->cards()->create([
            'title' => $validated['title'], // Убедитесь, что 'title' передается правильно
            'description' => $validated['description'], // И 'description'
        ]);
        $card->users()->attach($user->id, ['role' => 'owner']); // Назначаем роль owner
        return response()->json(['message' => 'Card created successfully', 'card' => $card], 201);
    }

  // Получение конкретной карточки
    public function show($boardId, $cardId) // Принимаем boardId и cardId как параметры
    {
        /*
        $userRole = request('user_role'); // Получаем роль, переданную из middleware

        if ($userRole !== 'viewer' && $userRole !== 'editor' && $userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
            */
      $board = Auth::user()->boards()->findOrFail($boardId);
      $card = $board->cards()->findOrFail($cardId); // Находим карточку по ID

      return response()->json($card);
    }

  // Обновление информации о карточке
    public function update(Request $request, $boardId, $cardId) // Принимаем boardId и cardId как параметры
    {
        /*
        $userRole = request('user_role'); // Получаем роль, переданную из middleware

        if ($userRole !== 'editor' && $userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
            */
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $board = Auth::user()->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId); // Находим карточку по ID
        $card->update($validated); // Обновляем карточку

        return response()->json(['message' => 'Card updated successfully', 'card' => $card]);
    }

   // Удаление доски
    public function destroy($boardId, $cardId)
    {
        /*
        $userRole = request('user_role'); // Получаем роль, переданную из middleware
        if ($userRole !== 'editor' && $userRole !== 'owner') 
        {
            return response()->json(['error' => 'Access denied'], 403);
        }
            */
        $board = Auth::user()->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);
        $card->delete();
        return response()->json(['message' => 'Card deleted successfully']);
     }
    public function addUserToCard(Request $request, $cardId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:viewer,editor',
        ]);
        /*
        $userRole = request('user_role');
        // Только владелец карточки может назначать роли
        if ($userRole !== 'owner')
        {
            return response()->json(['error' => 'Access denied'], 403);
        }
            */
        $card = Card::findOrFail($cardId);
        $card->users()->attach($request->user_id, ['role' => $request->role]);

        return response()->json(['message' => 'User added to card successfully']);
    }
    // Метод для получения карточек пользователя, к которым он имеет доступ, исключая свои
    public function getUserCards(Request $request)
    {
        $user = Auth::user();

        // Получаем карточки, к которым у пользователя есть доступ, исключая свои
        $cards = $user->cards()
            ->where('cards.user_id', '!=', $user->id) // Предполагается, что в карточке есть поле user_id
            ->get(['cards.id', 'cards.title', 'cards.board_id']); 

        return response()->json($cards);
    }
   
}
