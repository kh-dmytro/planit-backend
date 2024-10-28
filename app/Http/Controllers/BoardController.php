<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Board;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    // Получение всех досок текущего пользователя
    public function index()
    {
        // Получаем доски, принадлежащие текущему пользователю
        $boards = Auth::user()->boards;

        //return response()->json($boards);
        return response()->json(['boards' => $boards], 200);
    }

    // Создание новой доски
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Создаем доску для текущего пользователя
        $board = Auth::user()->boards()->create($validated);

        return response()->json(['message' => 'Board created successfully', 'board' => $board], 201);
    }

    // Получение конкретной доски
    public function show($id)
    {
        $board = Auth::user()->boards()->findOrFail($id);

        return response()->json($board);
    }

    // Обновление информации о доске
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $board = Auth::user()->boards()->findOrFail($id);
        $board->update($validated);

        return response()->json(['message' => 'Board updated successfully', 'board' => $board]);
    }

    // Удаление доски
    public function destroy($id)
    {
        $board = Auth::user()->boards()->findOrFail($id);
        $board->delete();

        return response()->json(['message' => 'Board deleted successfully']);
    }
}
