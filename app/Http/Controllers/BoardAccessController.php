<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Board; // если нужно
use App\Models\User;

class BoardAccessController extends Controller
{
    // Назначение доступа к доске
    public function assignUser(Request $request, $boardId)
    {
        $user = Auth::user();
        $board = $user->boards()->findOrFail($boardId);

        $userRole = request('user_role');

        if ($userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:view,edit,admin'
        ]);

        $assignedUser = User::findOrFail($request->user_id);

        $board->users()->syncWithoutDetaching([$assignedUser->id => ['role' => $request->role]]);

        return response()->json(['message' => 'Access assigned successfully.']);
    }

    // Удаление доступа к доске
    public function unassignUser(Request $request, $boardId)
    {
        $userRole = request('user_role');

        if ($userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
        $user = Auth::user();
        $board = $user->boards()->findOrFail($boardId);

        $request->validate(['user_id' => 'required|exists:users,id']);
        $assignedUser = User::findOrFail($request->user_id);

        $board->users()->detach($assignedUser->id);

        return response()->json(['message' => 'Access removed successfully.']);
    }
}
