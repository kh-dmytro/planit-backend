<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition()
    {
        return [
            'card_id' => Card::factory(), // автоматически создается карта
            // 'user_id' => User::factory(), // автоматически создается пользователь
            'file_path' => 'attachments/' . Str::random(10) . '.pdf',
            'file_name' => $this->faker->word . '.pdf',
            'file_type' => 'application/pdf',
        ];
    }
}
