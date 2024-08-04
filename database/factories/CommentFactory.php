<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\User\App\Models\User;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \App\Models\Comment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'parent_id' => $this->faker->optional(0.2)->uuid, // 20% chance of being null
            'user_id' => User::factory()->create()->id,
            'commentable_id' => function (array $attributes) {
                return $attributes['commentable_type']::factory();
            },
            'commentable_type' => $this->faker->randomElement(['App\Models\Post', 'App\Models\Review', 'App\Models\Comment']),
            'content' => $this->faker->paragraph,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
