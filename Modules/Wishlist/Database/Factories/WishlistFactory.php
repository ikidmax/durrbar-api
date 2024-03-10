<?php

namespace Modules\Wishlist\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WishlistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Wishlist\App\Models\Wishlist::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

