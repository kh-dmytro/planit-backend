<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_show()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id', 'name', 'email'
                 ]);
    }

    public function test_user_update()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->putJson('/api/user', [
            'name' => 'New Name',
            'email' => 'newemail@example.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'User updated successfully']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'newemail@example.com',
        ]);
    }

    public function test_user_destroy()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->deleteJson('/api/user');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'User deleted successfully']);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
