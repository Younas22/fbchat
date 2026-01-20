<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use App\Models\FacebookPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'page_id' => FacebookPage::factory(),
            'conversation_id' => $this->faker->unique()->numerify('t.##############'),
            'customer_name' => $this->faker->name(),
            'customer_psid' => $this->faker->numerify('##############'),
            'customer_profile_pic' => $this->faker->imageUrl(),
            'last_message_preview' => $this->faker->sentence(),
            'last_message_time' => now(),
            'is_archived' => false
        ];
    }
}