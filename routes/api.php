<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\TaskController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'show']);
    Route::put('user', [UserController::class, 'update']);
    Route::delete('user', [UserController::class, 'destroy']);

    Route::get('boards', [BoardController::class, 'index']); // Получить все доски пользователя
    Route::post('boards', [BoardController::class, 'store']); // Создать новую доску
    Route::get('boards/{id}', [BoardController::class, 'show']); // Получить конкретную доску
    Route::put('boards/{id}', [BoardController::class, 'update']); // Обновить доску
    Route::delete('boards/{id}', [BoardController::class, 'destroy']); // Удалить доску
    
    Route::get('boards/{boardId}/cards', [CardController::class, 'index']);
    Route::post('boards/{boardId}/cards', [CardController::class, 'store']);
    Route::get('boards/{boardId}/cards/{cardId}', [CardController::class, 'show']);
    Route::put('boards/{boardId}/cards/{cardId}', [CardController::class, 'update']);
    Route::delete('boards/{boardId}/cards/{cardId}', [CardController::class, 'destroy']);

    Route::get('boards/{boardId}/cards/{cardId}/checklists', [ChecklistController::class, 'index']);
    Route::post('boards/{boardId}/cards/{cardId}/checklists', [ChecklistController::class, 'store']);
    Route::get('boards/{boardId}/cards/{cardId}/checklists/{checklistId}', [ChecklistController::class, 'show']);
    Route::put('boards/{boardId}/cards/{cardId}/checklists/{checklistId}', [ChecklistController::class, 'update']);
    Route::delete('boards/{boardId}/cards/{cardId}/checklists/{checklistId}', [ChecklistController::class, 'destroy']);

    Route::get('boards/{boardId}/cards/{cardId}/checklists/{checklistId}/tasks', [TaskController::class, 'index']);
    Route::post('boards/{boardId}/cards/{cardId}/checklists/{checklistId}/tasks', [TaskController::class, 'store']);
    Route::get('boards/{boardId}/cards/{cardId}/checklists/{checklistId}/tasks/{taskId}', [TaskController::class, 'show']);
    Route::put('boards/{boardId}/cards/{cardId}/checklists/{checklistId}/tasks/{taskId}', [TaskController::class, 'update']);
    Route::delete('boards/{boardId}/cards/{cardId}/checklists/{checklistId}/tasks/{taskId}', [TaskController::class, 'destroy']);
    Route::put('boards/{boardId}/cards/{cardId}/checklists/{checklistId}/tasks/{taskId}/status', [TaskController::class, 'updateStatus']);

});

