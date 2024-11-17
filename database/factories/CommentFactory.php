<?php

namespace Database\Factories;
use App\Models\Comment;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'card_id' => \App\Models\Card::factory(),
            'content' => $this->faker->paragraph,
            'user_id' => \App\Models\User::factory(), // Свяжем с существующей доской во время теста
            'parent_id' => null,
           
        ];
    }
}

