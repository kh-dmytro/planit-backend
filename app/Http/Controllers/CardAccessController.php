<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Card; // если нужно
use App\Models\User;

class CardAccessController extends Controller
{
    // Назначение доступа к карточке
    public function assignUser(Request $request, $boardId, $cardId)
    {
        $userRole = request('user_role');
        // Только владелец карточки может назначать роли
        if ($userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
        $user = Auth::user();
        $board = $user->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:view,edit'
        ]);

        $assignedUser = User::findOrFail($request->user_id);

        $card->users()->syncWithoutDetaching([$assignedUser->id => ['role' => $request->role]]);

        return response()->json(['message' => 'Access assigned successfully.']);
    }

    // Удаление доступа к карточке
    public function unassignUser(Request $request, $boardId, $cardId)
    {
        $userRole = request('user_role');
        // Только владелец карточки может назначать роли
        if ($userRole !== 'owner') {
            return response()->json(['error' => 'Access denied'], 403);
        }
        $user = Auth::user();
        $board = $user->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);

        $request->validate(['user_id' => 'required|exists:users,id']);
        $assignedUser = User::findOrFail($request->user_id);

        $card->users()->detach($assignedUser->id);

        return response()->json(['message' => 'Access removed successfully.']);
    }
}
