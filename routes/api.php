<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BoardAccessController;
use App\Http\Controllers\CardAccessController;
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
//Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {


    Route::get('user', [UserController::class, 'show']);
    Route::put('user', [UserController::class, 'update']);
    Route::delete('user', [UserController::class, 'destroy']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Эти маршруты не требуют проверки доступа к доске 
    Route::get('boards', [BoardController::class, 'index']); // Получить все доски пользователя
    Route::post('boards', [BoardController::class, 'store']); // Создать новую доску
    Route::get('boards/accessible', [BoardController::class, 'getUserBoards']);

    // Группируем маршруты, требующие проверки доступа к доске
    Route::middleware(['check.board.access'])->group(function ()
    {
        
        Route::get('boards/{boardId}', [BoardController::class, 'show']); // Получить конкретную доску
        Route::put('boards/{boardId}', [BoardController::class, 'update']); // Обновить доску
        Route::delete('boards/{boardId}', [BoardController::class, 'destroy']); // Удалить доску
        Route::post('boards/{boardId}/assign', [BoardAccessController::class, 'assignUser']); // Назначить пользователя
        Route::delete('boards/{boardId}/unassign', [BoardAccessController::class, 'unassignUser']); // Удалить доступ у пользователя
        
        Route::get('cards/allowed', [CardController::class, 'getUserCards']);
        // Группа маршрутов для карточек и вложенных ресурсов
        Route::prefix('boards/{boardId}')->group(function () 
        {
            Route::get('cards', [CardController::class, 'index']); // Получить все карточки на доске
            Route::post('cards', [CardController::class, 'store']); // Создать новую карточку
            // Здесь мы добавляем маршруты карточек, которые требуют проверки доступа к доске
           // Route::middleware(['check.card.access'])->group(function () {
            
                Route::get('cards/{cardId}', [CardController::class, 'show']); // Получить конкретную карточку
                Route::put('cards/{cardId}', [CardController::class, 'update']); // Обновить карточку
                Route::delete('cards/{cardId}', [CardController::class, 'destroy']); // Удалить карточку

                // Маршруты для назначения доступа к карточке
                Route::post('cards/{cardId}/assign', [CardAccessController::class, 'assignUser']); // Назначить пользователя
                Route::delete('cards/{cardId}/unassign', [CardAccessController::class, 'unassignUser']); // Удалить доступ у пользователя

                // Маршруты для чеклистов
                Route::prefix('cards/{cardId}/checklists')->group(function () {
                    Route::get('/', [ChecklistController::class, 'index']); // Получить все чеклисты карточки
                    Route::post('/', [ChecklistController::class, 'store']); // Создать новый чеклист
                    Route::get('{checklistId}', [ChecklistController::class, 'show']); // Получить конкретный чеклист
                    Route::put('{checklistId}', [ChecklistController::class, 'update']); // Обновить чеклист
                    Route::delete('{checklistId}', [ChecklistController::class, 'destroy']); // Удалить чеклист
                });

                // Маршруты для задач
                Route::prefix('cards/{cardId}/checklists/{checklistId}/tasks')->group(function () {
                    Route::get('/', [TaskController::class, 'index']); // Получить все задачи чеклиста
                    Route::post('/', [TaskController::class, 'store']); // Создать новую задачу
                    Route::get('{taskId}', [TaskController::class, 'show']); // Получить конкретную задачу
                    Route::put('{taskId}', [TaskController::class, 'update']); // Обновить задачу
                    Route::delete('{taskId}', [TaskController::class, 'destroy']); // Удалить задачу
                    Route::put('{taskId}/status', [TaskController::class, 'updateStatus']); // Обновить статус задачи
                });
           // });
        });
    });
});


/*
Route::middleware(['auth:sanctum', 'check.board.access'])->group(function () {
    Route::get('user', [UserController::class, 'show']);
    Route::put('user', [UserController::class, 'update']);
    Route::delete('user', [UserController::class, 'destroy']);

    
    Route::get('boards', [BoardController::class, 'index']);
    Route::post('boards', [BoardController::class, 'store']);
    Route::get('boards/{id}', [BoardController::class, 'show']);
    Route::put('boards/{id}', [BoardController::class, 'update']);
    Route::delete('boards/{id}', [BoardController::class, 'destroy']);
    Route::post('boards/{boardId}/assign', [BoardAccessController::class, 'assignUser']); // Назначить пользователя
    Route::delete('boards/{boardId}/unassign', [BoardAccessController::class, 'unassignUser']); // Удалить доступ у пользователя


    // Группа маршрутов для карточек и вложенных ресурсов
    Route::prefix('boards/{boardId}')->group(function () {
        // Добавляем check.card.access к маршрутам карточек, чеклистов и задач
        Route::middleware(['check.card.access'])->group(function () {
            Route::get('cards', [CardController::class, 'index']);
            Route::post('cards', [CardController::class, 'store']);
            Route::get('cards/{cardId}', [CardController::class, 'show']);
            Route::put('cards/{cardId}', [CardController::class, 'update']);
            Route::delete('cards/{cardId}', [CardController::class, 'destroy']);

            // Маршруты для назначения доступа к карточке
            Route::post('cards/{cardId}/assign', [CardAccessController::class, 'assignUser']); // Назначить пользователя
            Route::delete('cards/{cardId}/unassign', [CardAccessController::class, 'unassignUser']); // Удалить доступ у пользователя
  
            // Маршруты для чеклистов
            Route::prefix('cards/{cardId}/checklists')->group(function () {
                Route::get('/', [ChecklistController::class, 'index']);
                Route::post('/', [ChecklistController::class, 'store']);
                Route::get('{checklistId}', [ChecklistController::class, 'show']);
                Route::put('{checklistId}', [ChecklistController::class, 'update']);
                Route::delete('{checklistId}', [ChecklistController::class, 'destroy']);
            });

            // Маршруты для задач
            Route::prefix('cards/{cardId}/checklists/{checklistId}/tasks')->group(function () {
                Route::get('/', [TaskController::class, 'index']);
                Route::post('/', [TaskController::class, 'store']);
                Route::get('{taskId}', [TaskController::class, 'show']);
                Route::put('{taskId}', [TaskController::class, 'update']);
                Route::delete('{taskId}', [TaskController::class, 'destroy']);
                Route::put('{taskId}/status', [TaskController::class, 'updateStatus']);
            });
        });
    });
});

*/