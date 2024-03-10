<?php

namespace Modules\Post\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Comment\App\Models\Comment;
use Modules\User\App\Models\User;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Post\App\Models\Post::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'publish' => fake()->randomElement(['draft', 'published']), // assuming publish is a string
            'content' => fake()->paragraph(),
            'cover_url' => fake()->imageUrl(),
            'author_id' => User::factory()->create()->id,
            'description' => fake()->sentence(),
            'total_views' => fake()->numberBetween(0, 10000),
            'total_shares' => fake()->numberBetween(0, 10000),
            'total_favorites' => fake()->numberBetween(0, 10000),

            'meta_title' => fake()->sentence(),
            'meta_keywords' => fake()->words(3, true),
            'meta_description' => fake()->sentence(),

            // 'comments'  => function () {
            //     return Comment::factory()->hasCommentable(3)->create();
            // },
        ];
    }
}
