<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Board;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($boardId, $cardId)
    {
        $board = Auth::user()->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);

        // Получаем комментарии к карточке с ответами и пользователем
        $comments = $card->comments()->whereNull('parent_id')->with('replies.user', 'user')->latest()->get();

        return response()->json($comments);
    }

    public function store(Request $request, $boardId, $cardId)
    {

        $validatedData = $request->validate([
            'content' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:comments,id'
        ], [], ['content' => 'content']);

        // Создание комментария, если валидация прошла
        $comment = Comment::create([
            'content' => $validatedData['content'],
            'card_id' => $cardId,
            'user_id' => auth()->id(),
            'parent_id' => $validatedData['parent_id'] ?? null,
        ]);

        return response()->json(['message' => 'Comment added successfully'], 201);
        /*
        $user = Auth::user();
        $board = $user->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);

        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        // Создаем комментарий (или ответ), связанный с карточкой и текущим пользователем
        $comment = $card->comments()->create([
            'user_id' => $user->id,
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment], 201);

        */
    }

    public function destroy($boardId, $cardId, $commentId)
    {
        $user = Auth::user();
        $comment = Comment::findOrFail($commentId); // Ищем комментарий независимо от пользователя

        // Проверяем, принадлежит ли комментарий текущему пользователю
        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
