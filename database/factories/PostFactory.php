<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(),
            'post_text' => fake()->paragraphs(asText: true),
        ];
    }
}
