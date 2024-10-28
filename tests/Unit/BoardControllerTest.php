<?php

namespace Tests\Unit;

use App\Http\Controllers\BoardController;
use App\Models\Board;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BoardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем пользователя и делаем его доступным во всех тестах
        $this->user = User::factory()->create();

        // Устанавливаем пользователя как аутентифицированного
        Auth::shouldReceive('user')->andReturn($this->user);
    }

    public function test_store_creates_new_board()
    {
        $request = Request::create('/boards', 'POST', [
            'title' => 'Test Board',
            'description' => 'Test Description'
        ]);

        $controller = new BoardController();
        $response = $controller->store($request);

        // Проверяем статус и возвращаемые данные
        $this->assertEquals(201, $response->status());
        $this->assertEquals('Test Board', $response->getData()->board->title);
    }

    public function test_index_returns_user_boards()
{
    $user = User::factory()->create();
    $boards = Board::factory()->count(3)->for($user)->create();

    Auth::shouldReceive('user')->once()->andReturn($user);

    $controller = new BoardController();
    $response = $controller->index();

    // Убедитесь, что ответ содержит доски пользователя
    $this->assertEquals($boards->toArray(), $response->getData(true)['boards']);
}

public function test_show_returns_specific_board()
{
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);

    Auth::shouldReceive('user')->once()->andReturn($user);

    $controller = new BoardController();
    $response = $controller->show($board->id);

    // Проверяем статус и возвращаемые данные
    $this->assertEquals(200, $response->status());
    $this->assertEquals($board->toArray(), $response->getData(true)['board']);
}

public function test_update_modifies_existing_board()
{
    $user = User::factory()->create();
    $board = Board::factory()->create(['user_id' => $user->id]);
    $request = Request::create('/boards/' . $board->id, 'PUT', [
        'title' => 'Updated Board',
        'description' => 'Updated Description'
    ]);

    Auth::shouldReceive('user')->once()->andReturn($user);

    $controller = new BoardController();
    $response = $controller->update($request, $board->id);

    // Проверяем статус и возвращаемые данные
    $this->assertEquals(200, $response->status());
    $this->assertEquals('Updated Board', $response->getData()->board->title);
}

    public function test_destroy_deletes_board()
    {
        $board = Board::factory()->create(['user_id' => $this->user->id]);

        $controller = new BoardController();
        $response = $controller->destroy($board->id);

        // Проверяем статус и сообщение
        $this->assertEquals(200, $response->status());
        $this->assertEquals('Board deleted successfully', $response->getData()->message);
    }
}
