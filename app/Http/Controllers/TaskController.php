<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index($boardId, $cardId, $checklistId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Получаем чек-лист
        $checklist = $user->boards()
            ->findOrFail($boardId)
            ->cards()
            ->findOrFail($cardId)
            ->checklists()
            ->findOrFail($checklistId);

        $tasks = $checklist->tasks; // Получаем задачи чек-листа

        return response()->json($tasks);
    }

    public function store(Request $request, $boardId, $cardId, $checklistId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $checklist = $user->boards()
            ->findOrFail($boardId)
            ->cards()
            ->findOrFail($cardId)
            ->checklists()
            ->findOrFail($checklistId);

        $task = $checklist->tasks()->create([
            'title' => $validated['title'],
            'is_completed' => false,
        ]);

        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    public function show($boardId, $cardId, $checklistId, $taskId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $checklist = $user->boards()
            ->findOrFail($boardId)
            ->cards()
            ->findOrFail($cardId)
            ->checklists()
            ->findOrFail($checklistId);

        $task = $checklist->tasks()->findOrFail($taskId);

        return response()->json($task);
    }

    public function update(Request $request, $boardId, $cardId, $checklistId, $taskId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $checklist = $user->boards()
            ->findOrFail($boardId)
            ->cards()
            ->findOrFail($cardId)
            ->checklists()
            ->findOrFail($checklistId);

        $task = $checklist->tasks()->findOrFail($taskId);
        $task->update($validated);

        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
    }
    public function updateStatus(Request $request, $boardId, $cardId, $checklistId, $taskId)
    {
        $validated = $request->validate([
            'is_completed' => 'required|boolean',
        ]);
        
        $user = Auth::user();
    
        // Проверка аутентификации пользователя
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Поиск доски, карточки, чеклиста и задачи
        $board = $user->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);
        $checklist = $card->checklists()->findOrFail($checklistId);
        $task = $checklist->tasks()->findOrFail($taskId);
    
        // Обновление состояния задачи
        $task->is_completed = $validated['is_completed'];
        $task->save();
    
        return response()->json(['message' => 'Task status updated successfully', 'task' => $task]);
    }
    


    public function destroy($boardId, $cardId, $checklistId, $taskId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $checklist = $user->boards()
            ->findOrFail($boardId)
            ->cards()
            ->findOrFail($cardId)
            ->checklists()
            ->findOrFail($checklistId);

        $task = $checklist->tasks()->findOrFail($taskId);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

}
