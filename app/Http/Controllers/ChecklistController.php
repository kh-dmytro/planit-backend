<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checklist;
use Illuminate\Support\Facades\Auth;

class ChecklistController extends Controller
{
    public function index($boardId, $cardId)
    {
        $user = Auth::user();
        $board = $user->boards()->find($boardId);
        $card = $board->cards()->find($cardId);
        /*
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Проверка на наличие доски, принадлежащей текущему пользователю
        $board = $user->boards()->find($boardId);
        if (!$board) {
            return response()->json(['error' => 'Board not found'], 404);
        }

        // Проверка на наличие карточки внутри найденной доски
        $card = $board->cards()->find($cardId);
        if (!$card) {
            return response()->json(['error' => 'Card not found'], 404);
        }
*/
        // Получаем все чек-листы для данной карточки
        $checklist = $card->checklists;

        return response()->json(['checklists' => $checklist], 200);
    }


    public function store(Request $request, $boardId, $cardId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $board = $user->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId); // Ищем карточку через доску

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Создаем чек-лист для найденной карточки
        $checklist = $card->checklists()->create($validated);

        return response()->json(['message' => 'Checklist created successfully', 'checklist' => $checklist], 201);
    }


    public function show($boardId, $cardId, $checklistId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $board = $user->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);
        $checklist = $card->checklists()->findOrFail($checklistId);

        return response()->json($checklist);
    }

    public function update(Request $request, $boardId, $cardId, $checklistId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $board = $user->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);
        $checklist = $card->checklists()->findOrFail($checklistId);
        $checklist->update($validated);

        return response()->json(['message' => 'Checklist updated successfully', 'checklist' => $checklist]);
    }

    public function destroy($boardId, $cardId, $checklistId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $board = $user->boards()->find($boardId);
        if (!$board) {
            return response()->json(['error' => 'Board not found'], 404);
        }
        $card = $board->cards()->findOrFail($cardId);
        if (!$card) {
            return response()->json(['error' => 'Card not found'], 404);
        }
        $checklist = $card->checklists()->findOrFail($checklistId);
        $checklist->delete();

        return response()->json(['message' => 'Checklist deleted successfully']);
    }
}
