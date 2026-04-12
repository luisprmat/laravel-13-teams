<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::withoutGlobalScopes()->get()->each(function (Category $category) {
            Post::factory()
                ->count(3)
                ->create([
                    'team_id' => $category->team_id,
                    'category_id' => $category->id,
                ]);
        });
    }
}
