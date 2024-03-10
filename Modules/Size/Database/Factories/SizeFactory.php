<?php

namespace Modules\Size\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Size\App\Models\Size::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

