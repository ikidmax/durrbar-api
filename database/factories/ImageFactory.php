<?php

namespace Database\Factories;

use App\Models\ECommerce\ECommerceProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \App\Models\Image::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        // Generate a dummy image and store it in the storage directory
        $fileName = $this->faker->word . '.jpg';
        $filePath = storage_path('app/public/images/' . $fileName);

        // Ensure the directory exists
        Storage::makeDirectory('public/images');

        // Create a dummy image
        $this->faker->image(storage_path('app/public/images'), 640, 480, null, false);

        return [
            'id' => Str::uuid(),
            'imageable_id' => ECommerceProduct::factory(), // Use the correct model
            'imageable_type' => ECommerceProduct::class, // Use the correct model
            'path' => 'images/' . $fileName,
        ];
    }
}
