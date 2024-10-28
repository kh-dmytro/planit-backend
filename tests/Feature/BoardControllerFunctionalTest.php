<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardControllerFunctionalTest extends TestCase
{
    /*
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Создаем пользователя и авторизуем его
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_get_all_boards()
    {
        $boards = Board::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson(route('boards.index'));

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_board()
    {
        $data = [
            'title' => 'New Board',
            'description' => 'A description for the board',
        ];

        $response = $this->postJson(route('boards.store'), $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'New Board']);
        
        $this->assertDatabaseHas('boards', ['title' => 'New Board']);
    }

    public function test_create_board_validation_error()
    {
        $data = [
            'title' => '',
        ];

        $response = $this->postJson(route('boards.store'), $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title']);
    }

    public function test_can_get_specific_board()
    {
        $board = Board::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson(route('boards.show', $board->id));

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $board->id, 'title' => $board->title]);
    }

    public function test_can_update_board()
    {
        $board = Board::factory()->create(['user_id' => $this->user->id]);
        $data = [
            'title' => 'Updated Board Title',
            'description' => 'Updated description',
        ];

        $response = $this->putJson(route('boards.update', $board->id), $data);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Updated Board Title']);

        $this->assertDatabaseHas('boards', ['id' => $board->id, 'title' => 'Updated Board Title']);
    }

    public function test_update_board_validation_error()
    {
        $board = Board::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson(route('boards.update', $board->id), ['title' => '']);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title']);
    }

    public function test_can_delete_board()
    {
        $board = Board::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson(route('boards.destroy', $board->id));

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Board deleted successfully']);

        $this->assertDatabaseMissing('boards', ['id' => $board->id]);
    }

    public function test_cannot_access_board_of_another_user()
    {
        $otherUser = User::factory()->create();
        $board = Board::factory()->create(['user_id' => $otherUser->id]);

        $this->getJson(route('boards.show', $board->id))->assertStatus(404);
        $this->putJson(route('boards.update', $board->id), ['title' => 'New Title'])->assertStatus(404);
        $this->deleteJson(route('boards.destroy', $board->id))->assertStatus(404);
    }
        */
}
