<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User\User;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \App\Models\Review::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reviewable_type' => 'Modules\Product\App\Models\Product', //  $this->faker->randomElement(['course', 'Modules\Product\App\Models\Product', 'tour']),
            'reviewable_id' => function (array $attributes) {
                return $attributes['reviewable_type']::factory();
            },
            'rating' => fake()->numberBetween(0, 5),
            'comment' => fake()->sentence,
            'helpful' => fake()->numberBetween(0, 100),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
