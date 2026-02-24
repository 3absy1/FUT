<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Bronze', 'min_points' => 0, 'max_points' => 999, 'sort_order' => 1],
            ['name' => 'Silver', 'min_points' => 1000, 'max_points' => 2999, 'sort_order' => 2],
            ['name' => 'Gold', 'min_points' => 3000, 'max_points' => 9999, 'sort_order' => 3],
            ['name' => 'Platinum', 'min_points' => 10000, 'max_points' => null, 'sort_order' => 4],
        ];

        foreach ($levels as $level) {
            Level::updateOrCreate(
                ['name' => $level['name']],
                $level
            );
        }
    }
}
