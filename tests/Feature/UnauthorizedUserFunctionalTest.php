<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Board;
use App\Models\Card;
use App\Models\Checklist;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnauthorizedUserFunctionalTest extends TestCase
{
   
    protected $user;
    protected $board;
    protected $card;
    protected $checklist;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->board = Board::factory()->create();
        $this->board->users()->attach($this->user->id, ['role' => 'owner']);
        $this->card = Card::factory()->create(['board_id' => $this->board->id]);
        $this->checklist = Checklist::factory()->create(['card_id' => $this->card->id]);
        $this->task = Task::factory()->create(['checklist_id' => $this->checklist->id]);
    }
    // profil functions test
    /** @test */
    public function unauthorized_user_cannot_get_profile()
    {
        // Неавторизованный доступ
        $response = $this->getJson("/api/user");

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);

    }
    /** @test */
    public function unauthorized_user_cannot_put_profile()
    {
        // Неавторизованный доступ
        $response = $this->putJson("/api/user",[
            'email' => 'NewEmail@mail.com',
            'name'=> 'new name',
        ]);

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);

    }
     /** @test */
    public function unauthorized_user_cannot_delete_profile()
    {
        // Неавторизованный доступ
        $response = $this->deleteJson("/api/user");
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
     /** @test */
    public function unauthorized_user_cannot_logout()
    {
        // Неавторизованный доступ
        $response = $this->postJson("/api/logout");
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }

    // Board functions test
    /** @test */
    public function unauthorized_user_cannot_get_boards()
    {
        // Неавторизованный доступ
        $response = $this->getJson("/api/boards/");

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }

    /** @test */
    public function unauthorized_user_cannot_get_one_boards()
    {
        $response = $this->getJson("/api/boards/{$this->board->id}");

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_create_boards()
    {
        $response = $this->postJson("/api/boards/",[
            'title' => 'Test Board',
            'description' => 'Board description'
        ]);
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_update_board()
    {
        $response = $this->putJson("/api/boards/{$this->board->id}",[
            'title' => 'Test Board',
            'description' => 'Board description'
        ]);
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_delete_board()
    {
        $response = $this->deleteJson("/api/boards/{$this->board->id}");
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_share_board()
    {
        $response = $this->postJson("/api/boards/{$this->board->id}/assign");
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_unshare_board()
    {
        $response = $this->deleteJson("/api/boards/{$this->board->id}/unassign");
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }


    // @card functions test
    /** @test */
    public function unauthorized_user_cannot_get_cards()
    {
        // Неавторизованный доступ
        $response = $this->getJson("/api/boards/{$this->board->id}/cards");

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_get_one_cards()
    {
         // Неавторизованный доступ
         $response = $this->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}");
 
         $response->assertStatus(401)
         ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_create_cards()
    {
        // Неавторизованный доступ
        $response = $this->postJson("/api/boards/{$this->board->id}/cards",[
            'title' => 'Test Card',
            'description' => 'card description'
        ]);
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_update_cards()
    {
        $response = $this->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}",[
            'title' => 'Test card',
            'description' => 'card description'
        ]);
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_delete_cards()
    {
        $response = $this->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}");
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_share_cards()
    {
        $response = $this->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/assign");
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_unshare_cards()
    {
        $response = $this->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/unassign");
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }

    /* @Checklist functions test */
    /** @test */
    public function unauthorized_user_cannot_get_checklists()
    {
        // Неавторизованный доступ
        $response = $this->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists");

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_get_one_checklist()
    {
         // Неавторизованный доступ
         $response = $this->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");
 
         $response->assertStatus(401)
         ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_create_checklists()
    {
        // Неавторизованный доступ
        $response = $this->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists",[
            'title' => 'Test checklist',
            'description' => 'checklist description'
        ]);
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_update_checklists()
    {
        $response = $this->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}",[
            'title' => 'Test checklist',
            'description' => 'card checklist'
        ]);
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_delete_checklists()
    {
        $response = $this->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }


    /* @Task functions test */
    /** @test */
    public function unauthorized_user_cannot_get_tasks()
    {
        // Неавторизованный доступ
        $response = $this->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks");

        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_get_one_task()
    {
         // Неавторизованный доступ
         $response = $this->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}");
 
         $response->assertStatus(401)
         ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_create_tasks()
    {
        // Неавторизованный доступ
        $response = $this->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks",[
            'title' => 'Test task'
        ]);
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_update_tasks()
    {
        $response = $this->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}",[
            'title' => 'Test task'
        ]);
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_update_status_tasks()
    {
        $response = $this->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}/status",[
           'is_completed' => true
        ]);
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function unauthorized_user_cannot_delete_tasks()
    {
        $response = $this->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}");
        $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
    }
   




}
