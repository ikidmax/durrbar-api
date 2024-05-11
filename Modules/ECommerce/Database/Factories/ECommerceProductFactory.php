<?php

namespace Modules\ECommerce\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ECommerceProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\ECommerce\App\Models\ECommerceProduct::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

