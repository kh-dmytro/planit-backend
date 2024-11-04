<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Board;
use App\Models\Card;
use App\Models\Checklist;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerFunctionalTest extends TestCase
{
    use RefreshDatabase;

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

        //$this->user->boards()->attach($this->board->id); // даем доступ пользователя к доске
    }

    
    /** @test */
    public function it_can_create_a_task()
    {
        // Создание чеклиста владельцем
        $response = $this->actingAs($this->user)->postJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks", [
            'title' => 'New Task'
        ]);
        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'New Task']);

        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }

    /** @test */
    
    public function it_can_list_tasks()
    {
        $response = $this->actingAs($this->user)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks");

        $response->assertStatus(200)
                 ->assertJsonCount(1) // предполагается, что есть одна задача
                 ->assertJsonFragment(['title' => $this->task->title]);
    }


    /** @test */
    public function it_can_show_a_task()
    {
        $response = $this->actingAs($this->user)->getJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}");


        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => $this->task->title]);
    }

    /** @test */
    public function it_can_update_a_task()
    {
      
        $response = $this->actingAs($this->user)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}", [
            'title' => 'Updated Task Title'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Updated Task Title']);

        $this->assertDatabaseHas('tasks', ['title' => 'Updated Task Title']);
    }

    /** @test */
    public function it_can_update_task_status()
    {
       
        $response = $this->actingAs($this->user)->putJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}/status", [
            'is_completed' => true
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['is_completed' => true]);

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'is_completed' => true]);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
       
        $response = $this->actingAs($this->user)->deleteJson("/api/boards/{$this->board->id}/cards/{$this->card->id}/checklists/{$this->checklist->id}/tasks/{$this->task->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', ['id' => $this->task->id]);
    }

    
}
