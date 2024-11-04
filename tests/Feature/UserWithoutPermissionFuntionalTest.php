<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Board;
use App\Models\Card;
use App\Models\Checklist;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserWithoutPermissionFuntionalTest extends TestCase
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
        $this->userWithoutAccess = User::factory()->create();
    }

     // Board functions test
    /** @test */
    
    public function without_access_user_can_get_accessible_boards()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->userWithoutAccess)->getJson("/api/boards/accessible");

        $response->assertStatus(200);
        //->assertJson(['error' => 'Access denied']);
    }

    /** @test */
    public function without_access_user_cannot_get_one_boards()
    {
        $response = $this->actingAs($this->userWithoutAccess)->getJson("/api/boards/{$this->board->id}");

        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    
    /** @test */
    /*
    public function without_access_user_cannot_create_boards()
    {
        $response = $this->actingAs($this->userWithoutAccess)->postJson("/api/boards/",[
            'title' => 'Test Board',
            'description' => 'Board description'
        ]);
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_update_board()
    {
        $response = $this->actingAs($this->userWithoutAccess)->putJson("/api/boards/{$this->board->id}",[
            'title' => 'Test Board',
            'description' => 'Board description'
        ]);
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_delete_board()
    {
        $response = $this->actingAs($this->userWithoutAccess)->deleteJson("/api/boards/{$this->board->id}");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_share_board()
    {
        $response = $this->actingAs($this->userWithoutAccess)->postJson("/api/boards/{$this->board->id}/assign");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_unshare_board()
    {
        $response = $this->actingAs($this->userWithoutAccess)->deleteJson("/api/boards/{$this->board->id}/unassign");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }

      // @card functions test
    /** @test */
    public function without_access_user_cannot_get_cards()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->userWithoutAccess)->getJson("/api/boards/{$this->board->id}/cards");

        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_get_one_cards()
    {
         // Неавторизованный доступ
         $response = $this->actingAs($this->userWithoutAccess)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}");
 
         $response->assertStatus(403)
         ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_create_cards()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->userWithoutAccess)->postJson("/api/boards/{$this->board->id}/cards",[
            'title' => 'Test Card',
            'description' => 'card description'
        ]);
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_update_cards()
    {
        $response = $this->actingAs($this->userWithoutAccess)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}",[
            'title' => 'Test card',
            'description' => 'card description'
        ]);
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_delete_cards()
    {
        $response = $this->actingAs($this->userWithoutAccess)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_share_cards()
    {
        $response = $this->actingAs($this->userWithoutAccess)->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/assign");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_unshare_cards()
    {
        $response = $this->actingAs($this->userWithoutAccess)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/unassign");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }

     /* @Checklist functions test */
    /** @test */
    public function without_access_user_cannot_get_checklists()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->userWithoutAccess)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists");

        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_get_one_checklist()
    {
         // Неавторизованный доступ
         $response = $this->actingAs($this->userWithoutAccess)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");
 
         $response->assertStatus(403)
         ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_create_checklists()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->userWithoutAccess)->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists",[
            'title' => 'Test checklist',
            'description' => 'checklist description'
        ]);
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_update_checklists()
    {
        $response = $this->actingAs($this->userWithoutAccess)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}",[
            'title' => 'Test checklist',
            'description' => 'card checklist'
        ]);
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_delete_checklists()
    {
        $response = $this->actingAs($this->userWithoutAccess)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }


    /* @Task functions test */
    /** @test */
    public function without_access_user_cannot_get_tasks()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->userWithoutAccess)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks");

        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_get_one_task()
    {
         // Неавторизованный доступ
         $response = $this->actingAs($this->userWithoutAccess)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}");
 
         $response->assertStatus(403)
         ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_create_tasks()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->userWithoutAccess)->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks",[
            'title' => 'Test task'
        ]);
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_update_tasks()
    {
        $response = $this->actingAs($this->userWithoutAccess)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}",[
            'title' => 'Test task'
        ]);
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_update_status_tasks()
    {
        $response = $this->actingAs($this->userWithoutAccess)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}/status",[
           'is_completed' => true
        ]);
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function without_access_user_cannot_delete_tasks()
    {
        $response = $this->actingAs($this->userWithoutAccess)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }

}
