<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Board;
use App\Models\Card;
use App\Models\Checklist;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChecklistControllerFunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected $ownerUser;
    protected $editorUser;
    protected $viewerUser;
    protected $unauthorizedUser;
    protected $board;
    protected $card;
    protected $checklist;

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

        // Добавление пользователей к карточке, заменяем attach на save
        $this->card->users()->save($this->ownerUser, ['role' => 'owner']);
        $this->card->users()->save($this->editorUser, ['role' => 'editor']);
        $this->card->users()->save($this->viewerUser, ['role' => 'viewer']);

        // Создание чеклиста для использования в тестах
        $this->checklist = Checklist::factory()->create(['card_id' => $this->card->id]);
    }

    /** @test */
    /*
    public function unauthorized_user_cannot_access_checklists()
    {
        // Неавторизованный доступ
        $response = $this->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists");

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);

        // Неавторизованное создание
        $response = $this->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists", []);

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);

        // Неавторизованный доступ к конкретному чеклисту
        $response = $this->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);

        // Неавторизованное обновление
        $response = $this->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}", []);

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);

        // Неавторизованное удаление
        $response = $this->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }

    /** @test */
    /*
    public function user_without_access_to_board_cannot_access_checklists()
    {
        $userWithoutAccess = User::factory()->create();
        $response = $this->actingAs($userWithoutAccess)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists");
        $response->assertStatus(403)
                 ->assertJson(['error' => 'Access denied']);
    }

    
    /** @test */
    public function owner_can_create_checklist()
    {
        // Создание чеклиста владельцем
        $response = $this->actingAs($this->ownerUser)->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists", [
            'title' => 'New Checklist',
            'description' => 'This is a checklist'
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Checklist created successfully']);

        $this->assertDatabaseHas('checklists', ['title' => 'New Checklist']);
    }

    /** @test */
    /*
    public function editor_can_create_checklist()
    {
        // Создание чеклиста редактором
        $response = $this->actingAs($this->editorUser)->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists", [
            'title' => 'New Checklist by Editor',
            'description' => 'This is a checklist by editor'
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Checklist created successfully']);
        
        $this->assertDatabaseHas('checklists', ['title' => 'New Checklist by Editor']);
    }

    /** @test */
    /*
    public function viewer_cannot_create_checklist()
    {
        // Попытка создания чеклиста зрителем
        $response = $this->actingAs($this->viewerUser)->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists", [
            'title' => 'Unauthorized Checklist',
            'description' => 'Should not be created'
        ]);

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Insufficient permissions to modify or delete']);
    }
        */
    /** @test */
    public function owner_can_view_checklist()
    {
        // Владелец может просмотреть чеклист
        $response = $this->actingAs($this->ownerUser)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");

        $response->assertStatus(200)
            ->assertJson($this->checklist->toArray());
    }

    /** @test */
    /*
    public function editor_can_view_checklist()
    {
        // Редактор может просмотреть чеклист
        $response = $this->actingAs($this->editorUser)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");

        $response->assertStatus(200)
                 ->assertJson($this->checklist->toArray());
    }

    /** @test */
    /*
    public function viewer_can_view_checklist()
    {
        // Зритель не может просмотреть чеклист
        $response = $this->actingAs($this->viewerUser)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");

        $response->assertStatus(200)
                ->assertJson($this->checklist->toArray());
    }

    /** @test */
    public function owner_can_update_checklist()
    {
        // Обновление чеклиста владельцем
        $response = $this->actingAs($this->ownerUser)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}", [
            'title' => 'Updated Checklist',
            'description' => 'Updated description'
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Checklist updated successfully']);

        $this->assertDatabaseHas('checklists', ['title' => 'Updated Checklist']);
    }

    /** @test */
    /*
    public function editor_can_update_checklist()
    {
        // Обновление чеклиста редактором
        $response = $this->actingAs($this->editorUser)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}", [
            'title' => 'Updated by Editor',
            'description' => 'Updated by editor'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Checklist updated successfully']);
        
        $this->assertDatabaseHas('checklists', ['title' => 'Updated by Editor']);
    }

    /** @test */
    /*
    public function viewer_cannot_update_checklist()
    {
        // Попытка обновления чеклиста зрителем
        $response = $this->actingAs($this->viewerUser)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}", [
            'title' => 'Unauthorized Update',
            'description' => 'Should not be updated'
        ]);

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Access denied']);
    }

    /** @test */
    public function owner_can_delete_checklist()
    {
        // Удаление чеклиста владельцем
        $response = $this->actingAs($this->ownerUser)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Checklist deleted successfully']);

        $this->assertDatabaseMissing('checklists', ['id' => $this->checklist->id]);
    }



    /** @test */
    public function test_checklist_status_updates_based_on_tasks()
    {
        $card = Card::factory()->create();
        $checklist = $card->checklists()->create(['title' => 'Checklist 1']);
        $task1 = $checklist->tasks()->create(['title' => 'Task 1', 'is_completed' => 'true']);
        $task2 = $checklist->tasks()->create(['title' => 'Task 2', 'is_completed' => 'true']);


        //$checklist->updateStatusBasedOnTasks();

        $this->assertEquals('completed', $checklist->status);

        $task3 = $checklist->tasks()->create(['title' => 'Task 3', 'is_completed' => 'false']);
        //$checklist->updateStatusBasedOnTasks();

        $this->assertEquals('active', $checklist->status);
    }
    /** @test */
    public function test_card_status_updates_based_on_checklists()
    {
        $card = Card::factory()->create();
        $checklist1 = $card->checklists()->create(['title' => 'Checklist 1']);
        $checklist2 = $card->checklists()->create(['title' => 'Checklist 2']);

        $checklist1->tasks()->create(['title' => 'Task 1', 'is_completed' => 'true']);
        $checklist1->tasks()->create(['title' => 'Task 2', 'is_completed' => 'true']);
        $checklist2->tasks()->create(['title' => 'Task 3', 'is_completed' => 'false']);

        $card->updateStatusBasedOnChecklists();

        $this->assertEquals('active', $card->status);

        $checklist2->tasks()->create(['title' => 'Task 4', 'is_completed' => 'true']);
        // $checklist2->updateStatusBasedOnTasks(); // Обновление статуса чеклиста

        // $card->updateStatusBasedOnChecklists();

        $this->assertEquals('active', $card->status); // Все чеклисты не завершены
    }

    /*
    public function editor_cannot_delete_checklist()
    {
        // Попытка удаления чеклиста редактором
        $response = $this->actingAs($this->editorUser)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Access denied']);
    }

    /** @test */
    /*
    public function viewer_cannot_delete_checklist()
    {
        // Попытка удаления чеклиста зрителем
        $response = $this->actingAs($this->viewerUser)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Access denied']);
    }
                 */
}
