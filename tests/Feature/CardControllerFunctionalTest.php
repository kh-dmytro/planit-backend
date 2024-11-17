<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Board;
use App\Models\Card;
use App\Models\Attachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile; // Импортируем класс UploadedFile
use Illuminate\Support\Facades\Storage; // Импортируем класс Storage

class CardControllerFunctionalTest extends TestCase
{
    //use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Создаем пользователей
        $this->ownerUser = User::factory()->create();
        $this->editorUser = User::factory()->create();
        $this->viewerUser = User::factory()->create();
        $this->unauthorizedUser = User::factory()->create();

        // Создаем доску
        $this->board = Board::factory()->create(['user_id' => $this->ownerUser->id]);
        $this->board->users()->attach($this->ownerUser->id, ['role' => 'owner']);
        $this->board->users()->attach($this->editorUser->id, ['role' => 'editor']);
        $this->board->users()->attach($this->viewerUser->id, ['role' => 'viewer']);

        // Создаем карточку
        $this->card = Card::factory()->create(['board_id' => $this->board->id]);
        $this->card->users()->attach($this->ownerUser->id, ['role' => 'owner']);
        $this->card->users()->attach($this->editorUser->id, ['role' => 'editor']);
        $this->card->users()->attach($this->viewerUser->id, ['role' => 'viewer']);
    }

    /** @test */
    public function test_owner_can_create_card()
    {
        $response = $this->actingAs($this->ownerUser)->postJson("/api/boards/{$this->board->id}/cards", [
            'title' => 'Test Card',
            'description' => 'Description of the test card'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Card created successfully',
                'card' => [
                    'title' => 'Test Card',
                    'description' => 'Description of the test card'
                ]
            ]);

        $this->assertDatabaseHas('cards', ['title' => 'Test Card']);
    }

    /** @test */
    public function test_owner_can_view_card()
    {
        $card = Card::factory()->create(['board_id' => $this->board->id]);
        $this->board->users()->attach($this->editorUser->id, ['role' => 'owner']);

        $response = $this->actingAs($this->ownerUser)->getJson("/api/boards/{$this->board->id}/cards/{$card->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $card->id,
                'title' => $card->title,
                'description' => $card->description
            ]);
    }



    /** @test */
    public function test_owner_can_delete_card()
    {
        $card = Card::factory()->create(['board_id' => $this->board->id]);

        $response = $this->actingAs($this->ownerUser)->deleteJson("/api/boards/{$this->board->id}/cards/{$card->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Card deleted successfully']);
        $this->assertDatabaseMissing('cards', ['id' => $card->id]);
    }

    /** @test */
    public function test_owner_can_upload_attachment_to_card()
    {
        // Аутентифицируемся под пользователем ownerUser
        //$this->actingAs($this->ownerUser);

        // Создание временного файла
        $file = UploadedFile::fake()->create('document.pdf', 1000); // 1 МБ

        // Выполнение запроса на загрузку файла
        $response = $this->actingAs($this->ownerUser)->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/attachments", [
            'file' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'File uploaded successfully',
                'attachment' => [
                    'file_name' => 'document.pdf',
                    'file_type' => 'application/pdf',
                ]
            ]);

        $this->assertDatabaseHas('attachments', [
            'file_name' => 'document.pdf'
            //'user_id' => $this->ownerUser->id,
        ]);
    }

    /** @test */
    public function test_owner_can_delete_attachment()
    {
        $this->actingAs($this->ownerUser); // Аутентификация пользователя

        // Создаем вложение
        $attachment = $this->card->attachments()->create([
            'file_path' => 'attachments/test.pdf',
            'file_name' => 'test.pdf',
            'file_type' => 'application/pdf',
            // 'user_id' => $this->ownerUser->id,
        ]);

        // Удаляем вложение
        $response = $this->actingAs($this->ownerUser)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/attachments/{$attachment->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'File deleted successfully']);

        // Проверка, что вложение было удалено из базы данных
        $this->assertDatabaseMissing('attachments', [
            'id' => $attachment->id,
        ]);
    }
}
