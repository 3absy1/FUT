<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['en' => 'Cairo', 'ar' => 'القاهرة'],
            ['en' => 'Giza', 'ar' => 'الجيزة'],
            ['en' => 'Alexandria', 'ar' => 'الإسكندرية'],
            ['en' => 'Mansoura', 'ar' => 'المنصورة'],
            ['en' => 'Tanta', 'ar' => 'طنطا'],
            ['en' => 'Ismailia', 'ar' => 'الإسماعيلية'],
            ['en' => 'Port Said', 'ar' => 'بورسعيد'],
            ['en' => 'Suez', 'ar' => 'السويس'],
        ];

        foreach ($areas as $area) {
            Area::updateOrCreate(
                ['name' => $area],
                [
                    'name' => $area,
                ]
            );
        }
    }
}
