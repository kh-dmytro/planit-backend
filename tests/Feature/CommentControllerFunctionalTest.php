<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Card;
use App\Models\Board;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentControllerFunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $card;

    protected function setUp(): void
    {
        parent::setUp();
        ini_set('memory_limit', '256M'); // Увеличиваем лимит памяти локально для теста
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->board = Board::factory()->create();
        $this->board->users()->attach($this->user->id, ['role' => 'owner']);
        $this->board->users()->attach($this->otherUser->id, ['role' => 'editor']);
        $this->card = Card::factory()->create(['board_id' => $this->board->id]);
    }

    /** @test */
    public function it_can_create_a_comment()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/comments", [
                'content' => 'This is a test comment'
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Comment added successfully']);

        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment',
            'card_id' => $this->card->id,
            'user_id' => $this->user->id,
            'parent_id' => null
        ]);
    }

    /** @test */
    public function it_can_create_a_reply_to_a_comment()
    {
        $comment = Comment::factory()->create(['card_id' => $this->card->id]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/comments", [
                'content' => 'This is a reply',
                'parent_id' => $comment->id
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Comment added successfully']);

        $this->assertDatabaseHas('comments', [
            'content' => 'This is a reply',
            'parent_id' => $comment->id,
            'card_id' => $this->card->id,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_get_all_comments_for_a_card()
    {
        $comments = Comment::factory()->count(3)->create(['card_id' => $this->card->id]);

        $response = $this->actingAs($this->user)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/comments");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_delete_own_comment()
    {
        $comment = Comment::factory()->create([
            'content' => 'This is a test comment',
            'card_id' => $this->card->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Comment deleted successfully']);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id
        ]);
    }

    /** @test */
    public function it_cannot_delete_comment_of_another_user()
    {
        // Убедимся, что пользователь и другой пользователь добавлены к доске
        $this->assertTrue($this->board->users->contains($this->user));
        $this->assertTrue($this->board->users->contains($this->otherUser));

        // Создаём комментарий другого пользователя
        $comment = Comment::factory()->create([
            'content' => 'This is a test comment',
            'card_id' => $this->card->id,
            'user_id' => $this->otherUser->id
        ]);

        // Проверяем наличие комментария в базе данных
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'This is a test comment',
            'user_id' => $this->otherUser->id,
            'card_id' => $this->card->id
        ]);

        // Пробуем удалить комментарий от имени пользователя без прав на удаление
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/comments/{$comment->id}");

        // Проверяем, что получен статус 403 и выводится правильное сообщение
        $response->assertStatus(403)
            ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function it_cannot_create_comment_with_invalid_data()
    {

        $response = $this->actingAs($this->user)
            ->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/comments", [
                'content' => ''  // Invalid content
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('messages.content.0', 'The content field is required.');
    }

    /** @test */
    public function it_cannot_create_comment_for_nonexistent_card()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/boards/{$this->board->id}/cards/999/comments", [
                'content' => 'This is a comment'
            ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_cannot_create_reply_for_nonexistent_comment()
    {

        $response = $this->actingAs($this->user)
            ->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/comments", [
                'content' => 'This is a reply',
                'parent_id' => 999  // Nonexistent comment ID
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('messages.parent_id.0', 'The selected parent id is invalid.');
    }
}
