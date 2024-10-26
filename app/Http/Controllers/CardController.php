<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    public function index($boardId) // Принимаем boardId как параметр
    {
        // Получаем доску, принадлежащую текущему пользователю
        $board = Auth::user()->boards()->findOrFail($boardId); // Находим доску по ID
        $cards = $board->cards; // Получаем карточки этой доски
 
        return response()->json($cards);
    }

    public function store(Request $request, $boardId)
{
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

    return response()->json(['message' => 'Card created successfully', 'card' => $card], 201);
}

  // Получение конкретной карточки
  public function show($boardId, $cardId) // Принимаем boardId и cardId как параметры
  {
      $board = Auth::user()->boards()->findOrFail($boardId);
      $card = $board->cards()->findOrFail($cardId); // Находим карточку по ID

      return response()->json($card);
  }

  // Обновление информации о карточке
  public function update(Request $request, $boardId, $cardId) // Принимаем boardId и cardId как параметры
  {
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
        $board = Auth::user()->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);
        $card->delete();

       return response()->json(['message' => 'Card deleted successfully']);
   }
}
