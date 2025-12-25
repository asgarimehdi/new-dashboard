<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $zanjan = Province::where('name', 'زنجان')->first();

        if (!$zanjan) return;

        $cities = [
            'زنجان',
            'ابهر',
            'خرمدره',
            'ماه‌نشان',
            'طارم',
        ];

        foreach ($cities as $city) {
            City::firstOrCreate([
                'province_id' => $zanjan->id,
                'name' => $city,
            ]);
        }
    }
}
