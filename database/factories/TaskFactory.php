<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Task;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'is_completed'=>false,
            'checklist_id' => \App\Models\Checklist::factory(), // Связываем с карточкой, если нужно
        ];
    }
}
