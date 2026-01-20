<?php

namespace Database\Factories;

use App\Models\SavedChat;
use App\Models\User;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavedChatFactory extends Factory
{
    protected $model = SavedChat::class;

    public function definition()
    {
        return [
            'conversation_id' => Conversation::factory(),
            'user_id' => User::factory(),
            'chat_id' => $this->faker->unique()->numerify('t.##############'),
            'notes' => $this->faker->paragraph()
        ];
    }
}