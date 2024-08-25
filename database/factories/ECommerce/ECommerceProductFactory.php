<?php

namespace Database\Factories\ECommerce;

use App\Models\ECommerce\ECommerceColor;
use App\Models\ECommerce\ECommerceGender;
use App\Models\ECommerce\ECommerceProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ECommerceProductFactory extends Factory
{
    protected $model = ECommerceProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate image paths from 1 to 24
        $allImages = array_map(function ($index) {
            return "uploads/product/image/product-{$index}.webp";
        }, range(1, 24));

        // Shuffle and select at least 8 images
        $selectedImages = $this->faker->randomElements($allImages, $this->faker->numberBetween(8, 24));

        return [
            'id' => Str::uuid(),
            'name' => $this->faker->word(),
            'sku' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'sub_description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'category' => $this->faker->randomElement(['Shoes', 'Apparel', 'Accessories']),
            'gender' => $this->faker->randomElement([['Men'], ['Women', 'Kids'], ['Kids']]),
            'publish' => $this->faker->randomElement(['published', 'draft']),
            'available' => $this->faker->numberBetween(0, 100),
            'price_sale' => $this->faker->randomFloat(2, 10, 1000),
            'taxes' => $this->faker->numberBetween(0, 20),
            'quantity' => $this->faker->numberBetween(0, 100),
            'inventory_type' => $this->faker->randomElement(['in stock', 'low stock', 'out of stock']),
            'new_label_enabled' => $this->faker->boolean,
            'new_label_content' => $this->faker->boolean ? 'NEW' : null,
            'sale_label_enabled' => $this->faker->boolean,
            'sale_label_content' => $this->faker->boolean ? 'SALE' : null,
            'total_sold' => $this->faker->numberBetween(0, 1000),

            'colors' => ECommerceColor::factory()->count(3)->create(),
            'genders' => ECommerceGender::inRandomOrder()->take(rand(1, 3))->get(),
            'images' => $selectedImages, // Use the selected images
        ];
    }
}
