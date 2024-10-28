<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Board;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardControllerFunctionalTest extends TestCase
{
    /*
    use RefreshDatabase;

    public function test_index_returns_cards_for_board()
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $cards = Card::factory()->count(3)->for($board)->create();

        $this->actingAs($user)
            ->getJson(route('cards.index', ['boardId' => $board->id]))
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonFragment(['title' => $cards->first()->title]);
    }

    public function test_store_creates_new_card_for_board()
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();

        $data = [
            'title' => 'New Card',
            'description' => 'Description for the new card',
        ];

        $this->actingAs($user)
            ->postJson(route('cards.store', ['boardId' => $board->id]), $data)
            ->assertStatus(201)
            ->assertJsonFragment(['title' => 'New Card']);

        $this->assertDatabaseHas('cards', [
            'title' => 'New Card',
            'board_id' => $board->id,
        ]);
    }

    public function test_show_returns_specific_card()
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $card = Card::factory()->for($board)->create();

        $this->actingAs($user)
            ->getJson(route('cards.show', ['boardId' => $board->id, 'cardId' => $card->id]))
            ->assertStatus(200)
            ->assertJsonFragment(['title' => $card->title]);
    }

    public function test_update_modifies_existing_card()
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $card = Card::factory()->for($board)->create();

        $data = [
            'title' => 'Updated Card Title',
            'description' => 'Updated Card Description',
        ];

        $this->actingAs($user)
            ->putJson(route('cards.update', ['boardId' => $board->id, 'cardId' => $card->id]), $data)
            ->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Card Title']);

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'title' => 'Updated Card Title',
        ]);
    }

    public function test_destroy_deletes_card()
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $card = Card::factory()->for($board)->create();

        $this->actingAs($user)
            ->deleteJson(route('cards.destroy', ['boardId' => $board->id, 'cardId' => $card->id]))
            ->assertStatus(200)
            ->assertJson(['message' => 'Card deleted successfully']);

        $this->assertDatabaseMissing('cards', ['id' => $card->id]);
    }
        */
}
