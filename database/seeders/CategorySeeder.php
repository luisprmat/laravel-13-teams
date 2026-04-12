<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Team;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::all()->each(function (Team $team) {
            Category::factory()
                ->count(3)
                ->create(['team_id' => $team->id]);
        });
    }
}
