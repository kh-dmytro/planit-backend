<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class BoardControllerFunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ownerUser = User::factory()->create();
        $this->editorUser = User::factory()->create();
        $this->viewerUser = User::factory()->create();
    }

    /** @test */
    public function test_owner_can_create_board()
    {
        $response = $this->actingAs($this->ownerUser)->postJson('/api/boards', [
            'title' => 'Test Board',
            'description' => 'Board description'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Board created successfully',
                'board' => [
                    'title' => 'Test Board',
                    'description' => 'Board description'
                ]
            ]);
        $this->assertDatabaseHas('boards', ['title' => 'Test Board']);
    }

    /** @test */
    /*public function test_non_owner_cannot_create_board()
    {
        $response = $this->actingAs($this->viewerUser)->postJson('/api/boards', [
            'title' => 'Unauthorized Board',
            'description' => 'This should fail'
        ]);

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Access denied']);
    }
*/
    /** @test */
    public function test_owner_can_view_own_board()
    {
        $board = Board::factory()->create(['user_id' => $this->ownerUser->id]);
        $board->users()->attach($this->ownerUser->id, ['role' => 'owner']);
        $response = $this->actingAs($this->ownerUser)->getJson("/api/boards/{$board->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $board->id,
                'title' => $board->title,
                'description' => $board->description,
            ]);
    }

    /** @test */
    /*
    public function test_editor_can_view_board()
    {
        $board = Board::factory()->create(['user_id' => $this->ownerUser->id]);
        $board->users()->attach($this->editorUser->id, ['role' => 'editor']);

        $response = $this->actingAs($this->editorUser)->getJson("/api/boards/{$board->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $board->id,
                     'title' => $board->title,
                     'description' => $board->description,
                 ]);
    }

    /** @test */
    /*
    public function test_viewer_can_only_view_board_and_not_update()
    {
        $board = Board::factory()->create(['user_id' => $this->ownerUser->id]);
        $board->users()->attach($this->viewerUser->id, ['role' => 'viewer']);

        $response = $this->actingAs($this->viewerUser)->getJson("/api/boards/{$board->id}");
        $response->assertStatus(200);

        $updateResponse = $this->actingAs($this->viewerUser)->putJson("/api/boards/{$board->id}", [
            'title' => 'Updated Title',
        ]);

        $updateResponse->assertStatus(403)
                       ->assertJson(['error' => 'Access denied']);
    }

    /** @test */
    /*
    public function test_editor_can_update_board()
    {
        $board = Board::factory()->create(['user_id' => $this->ownerUser->id]);
        $board->users()->attach($this->editorUser->id, ['role' => 'editor']);

        $response = $this->actingAs($this->editorUser)->putJson("/api/boards/{$board->id}", [
            'title' => 'Editor Updated Title',
            'description' => 'Updated description by editor'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Board updated successfully',
                     'board' => [
                         'title' => 'Editor Updated Title',
                         'description' => 'Updated description by editor'
                     ]
                 ]);
    }

  
    /** @test */
    public function test_owner_can_delete_board()
    {
        $board = Board::factory()->create(['user_id' => $this->ownerUser->id]);
        $board->users()->attach($this->ownerUser->id, ['role' => 'owner']);
        $response = $this->actingAs($this->ownerUser)->deleteJson("/api/boards/{$board->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Board deleted successfully']);
        $this->assertDatabaseMissing('boards', ['id' => $board->id]);
    }

    /** @test */
    /*
    public function test_owner_can_share_board()
    {
        $board = Board::factory()->create(['user_id' => $this->ownerUser->id]);
        $board->users()->attach($this->ownerUser->id, ['role' => 'owner']);
        $response = $this->actingAs($this->ownerUser)->postJson("/api/boards/{$board->id}/assign");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Board deleted successfully']);
        $this->assertDatabaseMissing('boards', ['id' => $board->id]);
    }
    */
}
