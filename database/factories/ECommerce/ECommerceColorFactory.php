<?php

namespace Database\Factories\ECommerce;

use App\Models\ECommerce\ECommerceColor;
use App\Models\ECommerce\ECommerceProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ECommerce\ECommerceColor>
 */
class ECommerceColorFactory extends Factory
{
    protected $model = ECommerceColor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'colorable_id' => ECommerceProduct::factory(),
            'colorable_type' => ECommerceProduct::class,
            'color' => $this->faker->safeHexColor(),
        ];
    }
}
