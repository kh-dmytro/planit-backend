<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Board; // если нужно
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BoardAccessController extends Controller
{
    // Назначение доступа к доске
    public function assignUser(Request $request, $boardId)
    {
        $userRole = $request->input('user_role');
        // Проверяем, имеет ли пользователь доступ к операции
        if ($userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
        $request->validate([
            'email' => 'required|email',
            'role' => 'nullable|in:owner,editor,viewer', // Допустимые роли
        ]);
        
    
        $user = User::where('email' , $request->email)->first();
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Проверка, есть ли уже доступ
        $board = Board::findOrFail($boardId);
        if ($board->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'User already has access'], 400);
        }
    
        // Получаем роль или устанавливаем по умолчанию
        $role = $request->input('role', 'viewer'); // Устанавливаем роль по умолчанию
    
        // Назначаем доступ
        $board->users()->attach($user->id, ['role' => $role]);
    
        return response()->json(['message' => 'Access granted'], 201);
    }

    // Удаление доступа к доске
    public function unassignUser(Request $request, $boardId)
    {
        $userRole = $request->input('user_role');

        // Проверяем, имеет ли пользователь доступ к операции
        if ($userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
    
        // Получаем текущего авторизованного пользователя
        $user = Auth::user();
    
        // Находим доску
        $board = $user->boards()->findOrFail($boardId);
    
        // Валидируем входные данные
        $request->validate(['email' => 'required|email|exists:users,email']);
    
        // Находим пользователя по email
        $assignedUser = User::where('email', $request->email)->firstOrFail();
    
        // Удаляем доступ пользователя к доске
        $board->users()->detach($assignedUser->id);
    
        return response()->json(['message' => 'Access removed successfully.'], 200);
    }
}
