<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \App\Models\Tag::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
