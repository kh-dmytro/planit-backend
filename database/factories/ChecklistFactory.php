<?php
namespace Database\Factories;

use App\Models\Checklist;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChecklistFactory extends Factory
{
    protected $model = Checklist::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'card_id' => \App\Models\Card::factory(), // Связываем с карточкой, если нужно
        ];
    }
}   