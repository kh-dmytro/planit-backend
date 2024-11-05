<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Board;
use App\Models\Card;
use App\Models\Checklist;
use App\Models\Task;

class BoardAccessControllerFunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected $owner;
    protected $board;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем пользователя с ролью owner и доску, на которой он владелец
        $this->owner = User::factory()->create();
        $this->board = Board::factory()->create();
        $this->board->users()->attach($this->owner->id, ['role' => 'owner']);
    }

    public function test_assign_user_as_owner()
    {
        $newUserEmail = 'newuser_' . uniqid() . '@example.com';
        $newUser = User::factory()->create(['email' => $newUserEmail]);

        $this->actingAs($this->owner)
            ->postJson("/api/boards/{$this->board->id}/assign", [
                'email' => $newUserEmail,
                'role' => 'editor',
                'user_role' => 'owner'
            ])
            ->assertStatus(201)
            ->assertJson(['message' => 'Access granted']);

        $this->assertDatabaseHas('board_user', [
            'board_id' => $this->board->id,
            'user_id' => $newUser->id,
            'role' => 'editor'
        ]);
    }

    public function test_assign_user_as_non_owner()
    {
        $viewer = User::factory()->create();
        $this->board->users()->attach($viewer->id, ['role' => 'viewer']);
        $newUserEmail = 'newuser_' . uniqid() . '@example.com';
        $newUser = User::factory()->create(['email' => $newUserEmail]);

        $this->actingAs($viewer)
            ->postJson("/api/boards/{$this->board->id}/assign", [
                'email' => $newUserEmail,
                'role' => 'editor',
                'user_role' => 'viewer'
            ])
            ->assertStatus(403)
            ->assertJson(['error' => 'Insufficient permissions to modify or delete']);
    }

    public function test_assign_user_with_invalid_email()
    {
        $this->actingAs($this->owner)
            ->postJson("/api/boards/{$this->board->id}/assign", [
                'email' => 'invalid-email',
                'role' => 'viewer',
                'user_role' => 'owner'
            ])
            ->assertStatus(422);
    }

    public function test_assign_user_already_has_access()
    {
        $existingUserEmail = 'existinguser_' . uniqid() . '@example.com';
        $existingUser = User::factory()->create(['email' => $existingUserEmail]);
        $this->board->users()->attach($existingUser->id, ['role' => 'viewer']);

        $this->actingAs($this->owner)
            ->postJson("/api/boards/{$this->board->id}/assign", [
                'email' => $existingUserEmail,
                'role' => 'editor',
                'user_role' => 'owner'
            ])
            ->assertStatus(400)
            ->assertJson(['message' => 'User already has access']);
    }

    public function test_unassign_user_as_owner()
    {
        $userToUnassignEmail = 'user_' . uniqid() . '@example.com';
        $userToUnassign = User::factory()->create(['email' => $userToUnassignEmail]);
        $this->board->users()->attach($userToUnassign->id, ['role' => 'viewer']);

        $this->actingAs($this->owner)
            ->deleteJson("/api/boards/{$this->board->id}/unassign", [
                'email' => $userToUnassignEmail,
                'user_role' => 'owner'
            ])
            ->assertStatus(200)
            ->assertJson(['message' => 'Access removed successfully.']);

        $this->assertDatabaseMissing('board_user', [
            'board_id' => $this->board->id,
            'user_id' => $userToUnassign->id
        ]);
    }

    public function test_unassign_user_as_non_owner()
    {
        $viewer = User::factory()->create();
        $userToUnassignEmail = 'user_' . uniqid() . '@example.com';
        $userToUnassign = User::factory()->create(['email' => $userToUnassignEmail]);
        $this->board->users()->attach($viewer->id, ['role' => 'viewer']);
        $this->board->users()->attach($userToUnassign->id, ['role' => 'viewer']);

        $this->actingAs($viewer)
            ->deleteJson("/api/boards/{$this->board->id}/unassign", [
                'email' => $userToUnassignEmail,
                'user_role' => 'viewer'
            ])
            ->assertStatus(403)
            ->assertJson(['error' => 'Insufficient permissions to modify or delete']);
    }

    public function test_unassign_user_not_on_board()
    {
        $userNotOnBoardEmail = 'notonboard_' . uniqid() . '@example.com';
        $userNotOnBoard = User::factory()->create(['email' => $userNotOnBoardEmail]);

        $this->actingAs($this->owner)
            ->deleteJson("/api/boards/{$this->board->id}/unassign", [
                'email' => $userNotOnBoardEmail,
                'user_role' => 'owner'
            ])
            ->assertStatus(200)
            ->assertJson(['message' => 'Access removed successfully.']);
    }
}
