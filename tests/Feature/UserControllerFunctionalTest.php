<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerFunctionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_profile()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'name' => $user->name,
                     'email' => $user->email,
                 ]);
    }

    public function test_user_can_update_profile()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->putJson('/api/user', [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'User updated successfully']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_user_can_delete_their_account()
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

    public function test_unauthenticated_user_cannot_access_profile()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_user_update_fails_with_invalid_email()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->putJson('/api/user', [
            'name' => 'Valid Name',
            'email' => 'not-a-valid-email', // Некорректный email
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_user_update_fails_with_missing_name()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->putJson('/api/user', [
            'name' => '', // Пустое имя, должно вызвать ошибку валидации
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['name']]);
    }

    public function test_user_update_fails_with_duplicate_email()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create(['email' => 'existing@example.com']);

        $this->actingAs($user);

        $response = $this->putJson('/api/user', [
            'name' => 'Valid Name',
            'email' => 'existing@example.com', // Email, который уже существует у другого пользователя
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_user_delete_when_not_authenticated()
    {
        // Попытка удалить пользователя без авторизации
        $response = $this->deleteJson('/api/user');

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_user_update_throws_exception()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Имитация ошибки при обновлении данных (например, если передать данные в неправильном формате)
        $response = $this->putJson('/api/user', [
            'name' => null, // Некорректное значение для имени
            'email' => 'updated@example.com',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['name']]);
    }
}
