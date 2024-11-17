<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AttachmentController extends Controller
{
    public function secureShow($fileName)
    {
        // Определите путь к файлу в защищённой директории
        $filePath = "private/attachments/{$fileName}";

        // Проверьте, существует ли файл
        if (!Storage::exists($filePath)) {
            abort(404, 'Файл не найден.');
        }

        // Выполните проверку прав доступа пользователя
        // Предположим, что у вас есть метод hasAccessToFile для проверки прав доступа
        if (!Auth::user() || !Auth::user()->hasAccessToFile($filePath)) {
            abort(403, 'У вас нет прав для доступа к этому файлу.');
        }

        // Возвращаем содержимое файла в ответе
        return response()->file(storage_path("app/{$filePath}"));
    }
}
