<?php

namespace Database\Factories\ECommerce;

use App\Models\ECommerce\ECommerceColor;
use App\Models\ECommerce\ECommerceGender;
use App\Models\ECommerce\ECommerceProduct;
use App\Models\ECommerce\ECommerceSize;
use App\Models\Image;
use App\Models\Tag;
use App\Models\Variant;
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
        $boolean = $this->faker->boolean;

        return [
            'id' => Str::uuid(),
            'name' => $this->faker->words(5, true),  // Generates a random product name
            'sku' => strtoupper(Str::random(10)),   // Generates a random SKU
            'description' => $this->faker->paragraph(),
            'sub_description' => $this->faker->sentence(100),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'category' => $this->faker->randomElement(['Shoes', 'Apparel', 'Accessories']),
            'publish' => $this->faker->randomElement(['published', 'draft']),
            'available' => $this->faker->numberBetween(0, 100),
            'price_sale' => $this->faker->randomFloat(2, 10, 1000),
            'taxes' => $this->faker->numberBetween(0, 20),
            'quantity' => $this->faker->numberBetween(0, 100),
            'inventory_type' => $this->faker->randomElement(['in stock', 'low stock', 'out of stock']),
            'new_label_enabled' => $boolean,
            'new_label_content' => $boolean ? 'NEW' : null,
            'sale_label_enabled' => !$boolean,
            'sale_label_content' => !$boolean ? 'SALE' : null,
            'total_sold' => $this->faker->numberBetween(0, 1000),
        ];
    }

    /**
     * Configure the factory with relationships.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (ECommerceProduct $product) {
            // Attach some random sizes, colors, and genders

            // Random Sizes
            $sizes = Variant::where('type', 'size')
                ->inRandomOrder()
                ->take(rand(2, 4)) // Select between 2 and 4 sizes
                ->get();

            // Random Colors
            $colors = Variant::where('type', 'color')
                ->inRandomOrder()
                ->take(rand(1, 3)) // Select between 1 and 3 colors
                ->get();

            // Random Genders
            $genders = Variant::where('type', 'gender')
                ->inRandomOrder()
                ->take(1) // Select a gender
                ->get();

            // Sync variants with the product (assuming you have a syncVariants method)
            $variantData = [
                'sizes' => $sizes->pluck('name')->toArray(),
                'colors' => $colors->pluck('name')->toArray(),
                'gender' => $genders->pluck('name')->toArray(),
            ];

            // Use the syncVariants trait method to sync the variants
            $product->syncVariants($product, $variantData);

            // Attach images
            foreach ($this->faker->randomElements(range(1, 24), rand(8, 24)) as $imageIndex) {
                Image::factory()->create([
                    'imageable_id' => $product->id,
                    'imageable_type' => ECommerceProduct::class,
                    'path' => "uploads/product/image/product-{$imageIndex}.webp",
                ]);
            }

            // Create new tags or fetch existing ones
            $tagNames = $this->faker->words(rand(2, 5)); // Generate 2 to 5 random tag names
            $tags = [];

            foreach ($tagNames as $tagName) {
                // Create or retrieve the tag
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tags[] = $tag->id; // Store the tag ID for attachment
            }

            // Attach multiple tags at once
            $product->attachTags($tags, 'productTag'); // Attach all created or retrieved tags to the product
        });
    }
}
