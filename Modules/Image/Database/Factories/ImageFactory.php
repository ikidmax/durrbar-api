<?php

namespace Modules\Image\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Image\App\Models\Image::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

