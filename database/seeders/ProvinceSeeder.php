<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            'زنجان',
          
        ];

        foreach ($provinces as $province) {
            Province::firstOrCreate([
                'name' => $province
            ]);
        }
    }
}
