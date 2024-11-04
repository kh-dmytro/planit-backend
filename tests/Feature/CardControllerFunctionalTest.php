<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Board;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class CardControllerFunctionalTest extends TestCase
{
    use RefreshDatabase;

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
    /*
    public function test_viewer_cannot_create_card()
    {
        $this->board->users()->attach($this->viewerUser->id, ['role' => 'viewer']);

        $response = $this->actingAs($this->viewerUser)->postJson("/api/boards/{$this->board->id}/cards", [
            'title' => 'Unauthorized Card',
            'description' => 'Should not be created'
        ]);

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Access denied']);
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
    /*
    public function test_editor_can_update_card()
    {
        $card = Card::factory()->create(['board_id' => $this->board->id]);
        $this->board->users()->attach($this->editorUser->id, ['role' => 'editor']);

        $response = $this->actingAs($this->editorUser)->putJson("/api/boards/{$this->board->id}/cards/{$card->id}", [
            'title' => 'Updated Card Title',
            'description' => 'Updated description'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Card updated successfully',
                     'card' => [
                         'title' => 'Updated Card Title',
                         'description' => 'Updated description'
                     ]
                 ]);
    }

    /** @test */
    /*
    public function test_viewer_cannot_update_card()
    {
        $card = Card::factory()->create(['board_id' => $this->board->id]);
        $this->board->users()->attach($this->viewerUser->id, ['role' => 'viewer']);

        $response = $this->actingAs($this->viewerUser)->putJson("/api/boards/{$this->board->id}/cards/{$card->id}", [
            'title' => 'Attempted Update',
        ]);

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Access denied']);
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
    /*
    public function test_viewer_cannot_delete_card()
    {
        $card = Card::factory()->create(['board_id' => $this->board->id]);
        $this->board->users()->attach($this->viewerUser->id, ['role' => 'viewer']);

        $response = $this->actingAs($this->viewerUser)->deleteJson("/api/boards/{$this->board->id}/cards/{$card->id}");

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Access denied']);
    }
     /** @test */
     /*
     public function viewer_on_board_but_editor_on_card_can_edit_card()
     {
         // Пользователь как viewer на уровне доски
         $this->board->users()->attach($this->viewerUser->id, ['role' => 'viewer']);
 
         // Создаем карточку и назначаем пользователю роль editor на уровне карточки
         $card = Card::factory()->create(['board_id' => $this->board->id]);
         $card->users()->attach($this->viewerUser->id, ['role' => 'editor']);
 
         // Тест: пользователь может редактировать карточку
         $response = $this->actingAs($this->viewerUser)->putJson("/api/boards/{$this->board->id}/cards/{$card->id}", [
             'title' => 'Updated Card Title',
             'description' => 'Updated description'
         ]);
 
         $response->assertStatus(200)
                  ->assertJson(['message' => 'Card updated successfully']);
     }
 
     /** @test */
     /*
     public function editor_on_board_but_viewer_on_card_cannot_edit_card()
     {
         // Пользователь как editor на уровне доски
         $this->board->users()->attach($this->editorUser->id, ['role' => 'editor']);
 
         // Создаем карточку и назначаем пользователю роль viewer на уровне карточки
         $card = Card::factory()->create(['board_id' => $this->board->id]);
         $card->users()->attach($this->editorUser->id, ['role' => 'viewer']);
 
         // Тест: пользователь не может редактировать карточку
         $response = $this->actingAs($this->editorUser)->putJson("/api/boards/{$this->board->id}/cards/{$card->id}", [
             'title' => 'Unauthorized Update Attempt',
             'description' => 'This update should fail'
         ]);
 
         $response->assertStatus(403)
                  ->assertJson(['error' => 'Access denied']);
     }
      /** @test */
      /*
    public function viewer_on_board_but_editor_on_card_can_delete_card()
    {
        $this->board->users()->attach($this->viewerUser->id, ['role' => 'viewer']);
        
        $card = Card::factory()->create(['board_id' => $this->board->id]);
        $card->users()->attach($this->viewerUser->id, ['role' => 'editor']);

        $response = $this->actingAs($this->viewerUser)->deleteJson("/api/boards/{$this->board->id}/cards/{$card->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Card deleted successfully']);
    }

    /** @test */
    /*
    public function editor_on_board_but_viewer_on_card_cannot_delete_card()
    {
        $this->board->users()->attach($this->editorUser->id, ['role' => 'editor']);
        
        $card = Card::factory()->create(['board_id' => $this->board->id]);
        $card->users()->attach($this->editorUser->id, ['role' => 'viewer']);

        $response = $this->actingAs($this->editorUser)->deleteJson("/api/boards/{$this->board->id}/cards/{$card->id}");

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Access denied']);
    }

    /** @test */
    /*
    public function unauthorized_user_cannot_create_card()
    {
        $response = $this->actingAs($this->unauthorizedUser)->postJson("/api/boards/{$this->board->id}/cards", [
            'title' => 'New Card',
            'description' => 'Card description'
        ]);

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Access denied']);
    }

    /** @test */
    /*
    public function unauthorized_user_cannot_delete_card()
    {
        $card = Card::factory()->create(['board_id' => $this->board->id]);

        $response = $this->actingAs($this->unauthorizedUser)->deleteJson("/api/boards/{$this->board->id}/cards/{$card->id}");

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Access denied']);
    }

    /** @test */
    /*
    public function unauthorized_user_cannot_view_or_edit_card()
    {
        $card = Card::factory()->create(['board_id' => $this->board->id]);

        // Attempt to view card
        $viewResponse = $this->actingAs($this->unauthorizedUser)->getJson("/api/boards/{$this->board->id}/cards/{$card->id}");
        $viewResponse->assertStatus(403)
                     ->assertJson(['error' => 'Access denied']);

        // Attempt to edit card
        $editResponse = $this->actingAs($this->unauthorizedUser)->putJson("/api/boards/{$this->board->id}/cards/{$card->id}", [
            'title' => 'Unauthorized Edit',
            'description' => 'Unauthorized attempt to edit'
        ]);
        $editResponse->assertStatus(403)
                     ->assertJson(['error' => 'Access denied']);
    }

     /** @test */
     /*
     public function authorized_user_cannot_view_card()
     {
         // Попытка просмотреть карточку пользователем без доступа
         $response = $this->actingAs($this->unauthorizedUser)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}");
 
         $response->assertStatus(403)
                  ->assertJson(['error' => 'Access denied']);
     }
 
     /** @test */
     /*
     public function authorized_user_cannot_edit_card()
     {
         // Попытка редактировать карточку пользователем без доступа
         $response = $this->actingAs($this->unauthorizedUser)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}", [
             'title' => 'Attempt to Edit',
             'description' => 'Unauthorized edit attempt'
         ]);
 
         $response->assertStatus(403)
                  ->assertJson(['error' => 'Access denied']);
     }
 
     /** @test */
     /*
     public function authorized_user_cannot_delete_card()
     {
         // Попытка удалить карточку пользователем без доступа
         $response = $this->actingAs($this->unauthorizedUser)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}");
 
         $response->assertStatus(403)
                  ->assertJson(['error' => 'Access denied']);
     }
   */
}
