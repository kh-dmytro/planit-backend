<?php

namespace Database\Factories;

use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'board_id' => \App\Models\Board::factory(), // Свяжем с существующей доской во время теста
            'status' => 'active',
            'priority' => 'medium',
        ];
    }
}
