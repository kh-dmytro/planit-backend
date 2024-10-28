<?php

namespace Tests\Unit;

use App\Http\Controllers\CardController;
use App\Models\Board;
use App\Models\Card;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class CardControllerTest extends TestCase
{
    /*
    use RefreshDatabase;

    public function test_index_returns_cards_for_board()
    {
        $user = User::Mockery()->create();
        $board = Board::Mockery()->for($user)->create();
        $cards = Card::factory()->count(3)->for($board)->create();

        Auth::shouldReceive('user')->once()->andReturn($user);

        $controller = new CardController();
        $response = $controller->index($board->id);

        $this->assertEquals($cards->toArray(), $response->getData(true));
    }

    public function test_store_creates_new_card()
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $request = Request::create('/boards/' . $board->id . '/cards', 'POST', [
            'title' => 'New Card',
            'description' => 'Description for the new card'
        ]);

        Auth::shouldReceive('user')->once()->andReturn($user);
        $user->shouldReceive('boards->find')->once()->with($board->id)->andReturn($board);
        $board->shouldReceive('cards->create')->once()->with([
            'title' => 'New Card',
            'description' => 'Description for the new card'
        ])->andReturn(new Card(['title' => 'New Card', 'description' => 'Description for the new card']));

        $controller = new CardController();
        $response = $controller->store($request, $board->id);

        $this->assertEquals(201, $response->status());
        $this->assertEquals('New Card', $response->getData()->card->title);
    }

    public function test_show_returns_specific_card()
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $card = Card::factory()->for($board)->create();

        Auth::shouldReceive('user')->once()->andReturn($user);
        $user->shouldReceive('boards->findOrFail')->once()->with($board->id)->andReturn($board);
        $board->shouldReceive('cards->findOrFail')->once()->with($card->id)->andReturn($card);

        $controller = new CardController();
        $response = $controller->show($board->id, $card->id);

        $this->assertEquals(200, $response->status());
        $this->assertEquals($card->toArray(), $response->getData(true));
    }

    public function test_update_modifies_existing_card()
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $card = Card::factory()->for($board)->create();
        $request = Request::create('/boards/' . $board->id . '/cards/' . $card->id, 'PUT', [
            'title' => 'Updated Card Title',
            'description' => 'Updated Card Description'
        ]);

        Auth::shouldReceive('user')->once()->andReturn($user);
        $user->shouldReceive('boards->findOrFail')->once()->with($board->id)->andReturn($board);
        $board->shouldReceive('cards->findOrFail')->once()->with($card->id)->andReturn($card);
        $card->shouldReceive('update')->once()->with([
            'title' => 'Updated Card Title',
            'description' => 'Updated Card Description'
        ])->andReturn(true);

        $controller = new CardController();
        $response = $controller->update($request, $board->id, $card->id);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Updated Card Title', $response->getData()->card['title']);
    }

    public function test_destroy_deletes_card()
    {
        $user = User::factory()->create();
        $board = Board::factory()->for($user)->create();
        $card = Card::factory()->for($board)->create();

        Auth::shouldReceive('user')->once()->andReturn($user);
        $user->shouldReceive('boards->findOrFail')->once()->with($board->id)->andReturn($board);
        $board->shouldReceive('cards->findOrFail')->once()->with($card->id)->andReturn($card);
        $card->shouldReceive('delete')->once()->andReturn(true);

        $controller = new CardController();
        $response = $controller->destroy($board->id, $card->id);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Card deleted successfully', $response->getData()->message);
    }
        */
}
