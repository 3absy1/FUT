<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $checkpointMap = [
            // Division 10 is easiest, Division 1 is hardest.
            10 => [2, 4, 6, 8],
            9 => [3, 6, 8],
            8 => [3, 6],
            7 => [4, 7],
            6 => [4],
            5 => [5],
            4 => [6],
            3 => [7],
            2 => [8],
            1 => [9],
        ];

        for ($division = 1; $division <= 10; $division++) {
            Division::updateOrCreate(
                ['sort_order' => $division],
                [
                    'name' => [
                        'en' => "Division {$division}",
                        'ar' => "الدرجة {$division}",
                    ],
                    'matches_count' => 10,
                    'checkpoints' => $checkpointMap[$division],
                    'exp_win' => 5,
                    'draw_exp' => 2,
                ]
            );
        }
    }
}

