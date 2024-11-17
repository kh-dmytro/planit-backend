<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;
use Spatie\FlareClient\Time\Time;

class CardController extends Controller
{
    public function index($boardId) // Принимаем boardId как параметр
    {

        // Получаем доску, принадлежащую текущему пользователю
        $board = Auth::user()->boards()->findOrFail($boardId); // Находим доску по ID
        //$cards = $board->cards; // Получаем карточки этой доски

        $cards = $board->cards()->with('attachments')->get();
        //return response()->json($cards);
        return response()->json(['cards' => $cards], 200);
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
        $card->users()->attach($user->id, ['role' => 'owner']); // Назначаем роль owner
        return response()->json(['message' => 'Card created successfully', 'card' => $card], 201);
    }

    // Получение конкретной карточки
    public function show($boardId, $cardId) // Принимаем boardId и cardId как параметры
    {

        $board = Auth::user()->boards()->findOrFail($boardId);
        //$card = $board->cards()->findOrFail($cardId); // Находим карточку по ID
        $card = $board->cards()->with('attachments')->findOrFail($cardId);

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
    public function addUserToCard(Request $request, $cardId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:viewer,editor',
        ]);

        $card = Card::findOrFail($cardId);
        $card->users()->attach($request->user_id, ['role' => $request->role]);

        return response()->json(['message' => 'User added to card successfully']);
    }
    // Метод для получения карточек пользователя, к которым он имеет доступ, исключая свои
    public function getUserCards(Request $request)
    {
        $user = Auth::user();

        // Получаем карточки, к которым у пользователя есть доступ, исключая свои
        $cards = $user->cards()
            ->where('cards.user_id', '!=', $user->id) // Предполагается, что в карточке есть поле user_id
            ->get(['cards.id', 'cards.title', 'cards.board_id']);

        return response()->json($cards);
    }
    // Метод для загрузки вложения
    public function uploadAttachment11(Request $request, $boardId, $cardId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $board = $user->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);

        $request->validate([
            'file' => 'required|file|max:5120',
        ]);

        $file = $request->file('file');

        // Генерация уникального имени файла
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Сохранение файла в директорию
        $filePath = $file->storeAs('storage/public/attachments', $fileName);

        //$filePath = $file->storeAs('attachments', $fileName, 'public');
        //$filePath = $file->store('attachments', 'public');

        $attachment = $card->attachments()->create([
            //  'user_id' => $user->id,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
        ]);
        return response()->json([
            'message' => 'File uploaded successfully1',
            'attachment' => $attachment,
        ], 201);
        /*
        // Создание записи в базе данных
        $attachment = Attachment::create([
            'card_id' => $cardId,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            // Убедитесь, что user_id добавляется, если требуется
            //'user_id' => auth()->id(),
        ]);


/*
        $file = $request->file('file');
        //$filePath = $file->store('attachments', 'public');

        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs('public/attachments', $fileName);
        $fileType = $file->getMimeType();

        // Сохраняем вложение и явно указываем `user_id`
        $attachment = $card->attachments()->create([
            //  'user_id' => $user->id,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'attachment' => $attachment,
        ], 201);

        */
    }

    public function uploadAttachment(Request $request, $boardId, $cardId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Проверяем, существует ли board и card, к которым добавляется вложение
        $board = $user->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);

        $request->validate([
            'file' => 'required|file|max:5120',
        ]);

        $file = $request->file('file');


        // Сохраняем файл на диск 'public' в папку 'attachments'
        // $filePath = $file->store('attachments', 'public');
        $fileName =  date('Y-m-d_His') . '_' . $user->id . '_' . $file->getClientOriginalName();
        //$fileName = $file->getClientOriginalName();
        $fileType = $file->getMimeType();

        $filePath = $file->storeAs('attachments', $fileName); //functionierte!!!
        // $filePath = $file->storeAs('private/attachments', $fileName); //secure access

        //$filePath = $file->move(storage_path('app/public/attachments'), $fileName);
        if (!$filePath) {
            return response()->json(['error' => 'File could not be saved'], 500);
        }
        // Создаем запись в базе данных с данными о файле
        $attachment = $card->attachments()->create([
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            //'user_id' => $user->id, // Добавьте, если нужно связывать с пользователем
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'attachment' => $attachment,
        ], 201);
    }





    // Метод для удаления вложения
    public function deleteAttachment($boardId, $cardId, $attachmentId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        //return response()->json(['message' => 'File ' . $attachmentId . ' successfully'], 200);
        $board = $user->boards()->findOrFail($boardId);
        $card = $board->cards()->findOrFail($cardId);
        //$card = Card::findOrFail($cardId);
        $attachment = $card->attachments()->findOrFail($attachmentId);

        if ($attachment->card_id !== $card->id) {
            return response()->json(['message' => 'Attachment not found'], 4044);
        }

        // Удалить файл из хранилища
        Storage::disk('public')->delete($attachment->file_path);

        // Удалить запись из базы данных
        $attachment->delete();

        return response()->json(['message' => 'File deleted successfully'], 200);
    }
}
