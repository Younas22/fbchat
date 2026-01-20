<?php

namespace Database\Factories;

use App\Models\FacebookPage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacebookPageFactory extends Factory
{
    protected $model = FacebookPage::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'page_id' => $this->faker->unique()->numerify('##############'),
            'page_name' => $this->faker->company(),
            'page_access_token' => 'EAA' . $this->faker->sha256(),
            'page_profile_pic' => $this->faker->imageUrl(),
            'is_active' => true,
            'connected_at' => now()
        ];
    }
}