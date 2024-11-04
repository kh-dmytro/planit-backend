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
      
        $userRole = request('user_role'); // Роль пользователя, переданная middleware

       
        $boards = Auth::user()->boards;
        //return response()->json($boards);
        return response()->json(['boards' => $boards], 200);
    }

    // Создание новой доски
    public function store(Request $request)
    {
        \Log::info('Request data:', $request->all());
      
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    
        // Убедитесь, что текущий пользователь авторизован
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized (board)'], 401);
        }
    
        // Создаем доску для текущего пользователя
        $board = $user->boards()->create(array_merge($validated, ['user_id' => $user->id]));
        try {
            // Логируем перед добавлением
            \Log::info('Attempting to attach user with role owner:', [
                'board_id' => $board->id,
                'user_id' => $user->id
            ]);
    
            // Проверяем, есть ли уже связь
            $existingUser = $board->users()->where('user_id', $user->id)->first();
            
            if (!$existingUser) {
                $board->users()->attach($user->id, ['role' => 'owner']); // Назначаем роль owner
                \Log::info('User successfully attached to board:', [
                    'board_id' => $board->id,
                    'user_id' => $user->id,
                    'role' => 'owner'
                ]);
            } else {
                \Log::info('User already exists in board:', [
                    'board_id' => $board->id,
                    'user_id' => $user->id,
                    'existing_role' => $existingUser->pivot->role
                ]);
            }
    
        } catch (\Exception $e) {
            \Log::error('Error attaching user to board: ' . $e->getMessage());
        }
        
        // Проверяем, что пользователь успешно присоединен
        $attachedUser = $board->users()->where('user_id', $user->id)->first();
        \Log::info('Attached user data:', $attachedUser ? $attachedUser->toArray() : 'No user found');
    
        return response()->json(['message' => 'Board created successfully', 'board' => $board], 201);
    }

    // Получение конкретной доски
    public function show($id)
    {
        /*
        $userRole = request('user_role'); // Роль пользователя, переданная middleware

        if ($userRole !== 'viewer' && $userRole !== 'editor' && $userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
            */
        $board = Auth::user()->boards()->findOrFail($id);

        return response()->json($board, 200);
    }

    // Обновление информации о доске
    public function update(Request $request, $id)
    {
        /*
        $userRole = request('user_role');

        if ($userRole !== 'editor' && $userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
            */
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
        /*
        $userRole = request('user_role');

        if ($userRole !== 'editor' && $userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }

        */
        $board = Auth::user()->boards()->findOrFail($id);
        $board->delete();

        return response()->json(['message' => 'Board deleted successfully']);
    }
   /* public function addUserToBoard(Request $request, $boardId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:viewer,editor',
        ]);

        $userRole = request('user_role');

        if ($userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $board = Auth::user()->boards()->findOrFail($boardId);
        $board->users()->attach($request->user_id, ['role' => $request->role]);

        return response()->json(['message' => 'User added to board successfully'], 200);
    }*/
    public function getUserBoards(Request $request)
    {
        $user = Auth::user();
        
        // Получаем доски, к которым у пользователя есть доступ, исключая свои
        $boards = $user->boards()
            ->where('board_user.role', '!=', 'owner')
            ->get(['boards.id', 'boards.title', 'boards.description']);
          
        return response()->json($boards);
    }
    
}
