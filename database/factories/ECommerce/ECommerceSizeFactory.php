<?php

namespace Database\Factories\ECommerce;

use App\Models\ECommerce\ECommerceProduct;
use App\Models\ECommerce\ECommerceSize;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ECommerce\ECommerceSize>
 */
class ECommerceSizeFactory extends Factory
{
    protected $model = ECommerceSize::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'sizeable_id' => ECommerceProduct::factory(),
            'sizeable_type' => ECommerceProduct::class,
            'size' => $this->faker->randomElement(['7', '8', '8.5', '9', '9.5', '10', '10.5', '11', '11.5', '12', '13']),
        ];
    }
}
