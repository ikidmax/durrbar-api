<?php

namespace Modules\Color\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ColorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Color\App\Models\Color::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->safeColorName(),
            'code' => fake()->hexColor(),
        ];
    }
}
