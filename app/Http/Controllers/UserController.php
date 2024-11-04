<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function show(Request $request)
{
    try {
        $user = $request->user();

        // Проверяем, авторизован ли пользователь
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($user);
    } catch (\Exception $e) {
        return response()->json(['error' =>" An error occurred: " . $e->getMessage()], 500);
    }
}

public function update(Request $request)
{
    try {
        $user = $request->user();

        // Проверяем, авторизован ли пользователь
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Валидация данных
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        // Обновляем пользователя
        $user->update($request->only('name', 'email'));

        return response()->json(['message' => 'User updated successfully']);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['errors' => $e->validator->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}

public function destroy(Request $request)
{
    try {
        $user = $request->user();

        // Проверяем, авторизован ли пользователь
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Удаляем пользователя
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}
}
