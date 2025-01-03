<?php

namespace Database\Factories\ECommerce;

use App\Models\ECommerce\ECommerceGender;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ECommerceGenderFactory extends Factory
{
    protected $model = ECommerceGender::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker->randomElement(['Male', 'Female', 'Kids']),  // Generates a gender name
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
