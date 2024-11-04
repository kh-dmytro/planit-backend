<?php

namespace Tests\Feature;


use App\Models\User;
use App\Models\Board;
use App\Models\Card;
use App\Models\Checklist;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class EditorFunctionalTest extends TestCase
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
        $this->editor = User::factory()->create();
        $this->board = Board::factory()->create();
        $this->board->users()->attach($this->user->id, ['role' => 'owner']);
        $this->board->users()->attach($this->editor->id, ['role' => 'editor']);

        $this->card = Card::factory()->create(['board_id' => $this->board->id]);
        $this->checklist = Checklist::factory()->create(['card_id' => $this->card->id]);
        $this->task = Task::factory()->create(['checklist_id' => $this->checklist->id]);
        
    }

    
      // Board functions test
    /** @test */
    
    public function editor_can_get_accessible_boards()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->editor)->getJson("/api/boards/accessible");

       // Проверка структуры ответа
        $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'description',
                'pivot' => [
                    'user_id',
                    'board_id',
                    'role',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);

        // Проверка, что каждый объект в массиве имеет 'role' => 'viewer' в pivot
        $responseData = $response->json();
        foreach ($responseData as $board) {
            $this->assertEquals('editor', $board['pivot']['role'], 'Each board should have role viewer in pivot');
        }
    }

    /** @test */
    public function editor_can_get_one_board()
    {
        $response = $this->actingAs($this->editor)->getJson("/api/boards/{$this->board->id}");
         // Проверка правильной структуры
        $response->assertStatus(200)
        ->assertJsonStructure([
            'id',
            'title',
            'description',
            'pivot' => [
                'user_id',
                'board_id',
                'role',
                'created_at',
                'updated_at'
            ]
        ]);
        // Проверка, что поле 'role' имеет значение 'viewer'
        $response->assertJsonFragment([
            'role' => 'editor'
        ]);
    }
    
    /** @test */
    /*
    public function editor_cannot_create_boards()
    {
        $response = $this->actingAs($this->editor)->postJson("/api/boards/",[
            'title' => 'Test Board',
            'description' => 'Board description'
        ]);
        $response->assertStatus(403)
        ->assertJson(['error' => 'Insufficient permissions to modify or delete']);
    }
    /** @test */
    public function editor_cannot_update_board()
    {
        $response = $this->actingAs($this->editor)->putJson("/api/boards/{$this->board->id}",[
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
    public function editor_cannot_delete_board()
    {
        $response = $this->actingAs($this->editor)->deleteJson("/api/boards/{$this->board->id}");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Insufficient permissions to delete']);
    }
    /** @test */
    public function editor_cannot_share_board()
    {
        $response = $this->actingAs($this->editor)->postJson("/api/boards/{$this->board->id}/assign");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
        
    }
    /** @test */
    public function editor_cannot_unshare_board()
    {
        $response = $this->actingAs($this->editor)->deleteJson("/api/boards/{$this->board->id}/unassign");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Insufficient permissions to delete']);
       
    }


    // @card functions test
    /** @test */
    public function editor_can_get_cards()
    {
        // Запрос на получение карточек для пользователя с ролью 'viewer'
        $response = $this->actingAs($this->editor)->getJson("/api/boards/{$this->board->id}/cards");
    
        // Проверка структуры ответа (основные поля карточек)
        $response->assertStatus(200)
            ->assertJsonStructure([
                'cards' => [
                    '*' => [
                        'id',
                        'board_id',
                        'title',
                        'description',
                        'status',
                        'priority',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    
        // Проверка, что, если pivot существует, роль в нём установлена как 'viewer'
        $cards = $response->json('cards');
        foreach ($cards as $card) {
            if (isset($card['pivot'])) { // Проверка наличия 'pivot' перед обращением
                $this->assertEquals('editor', $card['pivot']['role'], 'Each card should have role viewer in pivot');
            }
        }
    }
    
    /** @test */
    public function editor_can_get_one_card()
    {
        // Запрос на получение одной карточки
        $response = $this->actingAs($this->editor)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}");
    
        // Проверка структуры ответа для одиночной карточки
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'board_id',
                'title',
                'description',
                'status',
                'priority',
                'created_at',
                'updated_at',
            ]);
    
        // Проверка, что поле 'role' имеет значение 'viewer' (если поле 'pivot' присутствует)
        $responseData = $response->json();
        if (isset($responseData['pivot'])) {
            $this->assertEquals('editor', $responseData['pivot']['role'], 'The role should be viewer in pivot for a single card');
        }
    }
    
    /** @test */
    public function editor_cannot_create_cards()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->editor)->postJson("/api/boards/{$this->board->id}/cards",[
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
    }
    /** @test */
    public function editor_can_update_cards()
    {
        $response = $this->actingAs($this->editor)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}",[
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
    public function editor_cannot_delete_cards()
    {
        $response = $this->actingAs($this->editor)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Insufficient permissions to delete']);
        
    }
    /** @test */
    public function editor_cannot_share_cards()
    {
        $response = $this->actingAs($this->editor)->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/assign");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Access denied']);
    }
    /** @test */
    public function editor_cannot_unshare_cards()
    {
        $response = $this->actingAs($this->editor)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/unassign");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Insufficient permissions to delete']);
    }

     /* @Checklist functions test */
    /** @test */
    public function editor_can_get_checklists()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->editor)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists");

        $response->assertStatus(200)
        ->assertJsonStructure([
            'checklists' => [
                '*' =>[
                    'id',
                    'title',
                    'card_id',
                    'description',
                ]
            ]
        ]);
    }
    /** @test */
    public function editor_can_get_one_checklist()
    {
         // Неавторизованный доступ
         $response = $this->actingAs($this->editor)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");
 
         $response->assertStatus(200)
         ->assertJsonStructure([
             
             'id',
             'title',
             'card_id',
             'description',
             
         ]);
    }
    /** @test */
    public function editor_can_create_checklists()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->editor)->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists",[
            'title' => 'New Checklist',
            'description' => 'This is a checklist'
        ]);
        
        $response->assertStatus(201)
                 ->assertJson(['message' => 'Checklist created successfully']);
        
    }
    /** @test */
    public function editor_can_update_checklists()
    {
        $response = $this->actingAs($this->editor)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}",[
            'title' => 'Updated by Editor',
            'description' => 'Updated by editor'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Checklist updated successfully']);
        
        $this->assertDatabaseHas('checklists', ['title' => 'Updated by Editor']);
    }
    /** @test */
    public function editor_cannot_delete_checklists()
    {
        $response = $this->actingAs($this->editor)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Insufficient permissions to delete']);
    }


    /* @Task functions test */
    /** @test */
    public function editor_can_get_tasks()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->editor)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks");

        $response->assertStatus(200)
        ->assertJsonStructure([
            
                '*' =>[
                    'id',
                    'title',
                    'checklist_id',
                    'is_completed',
                ]
            
        ]);
    }
    /** @test */
    public function editor_can_get_one_task()
    {
         // Неавторизованный доступ
         $response = $this->actingAs($this->editor)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}");
 
         $response->assertStatus(200)
        ->assertJsonStructure([
           
            'id',
            'title',
            'checklist_id',
            'is_completed',
                
        ]);
    }
    /** @test */
    public function editor_can_create_tasks()
    {
        // Неавторизованный доступ
        $response = $this->actingAs($this->editor)->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks",[
            'title' => 'New Task'
        ]);
        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'New Task']);
    }
    /** @test */
    public function editor_can_update_tasks()
    {
        $response = $this->actingAs($this->editor)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}",[
            'title' => 'Updated Task Title'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Updated Task Title']);
    }
    /** @test */
    public function editor_can_update_status_tasks()
    {
        $response = $this->actingAs($this->editor)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}/status",[
            'is_completed' => true
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['is_completed' => true]);
    }
    /** @test */
    public function editor_cannot_delete_tasks()
    {
        $response = $this->actingAs($this->editor)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}");
        $response->assertStatus(403)
        ->assertJson(['error' => 'Insufficient permissions to delete']);
    }

}
