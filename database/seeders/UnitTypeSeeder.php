<?php

namespace Database\Seeders;

use App\Models\UnitType;
use Illuminate\Database\Seeder;

class UnitTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['title' => 'ستاد شبکه بهداشت و درمان', 'description' => 'واحد ستادی شهرستان'],
            ['title' => 'مرکز بهداشت شهرستان', 'description' => 'مرکز بهداشت سطح شهرستان'],
            ['title' => 'مرکز خدمات جامع سلامت شهری', 'description' => null],
            ['title' => 'مرکز خدمات جامع سلامت روستایی', 'description' => null],
            ['title' => 'پایگاه سلامت', 'description' => null],
            ['title' => 'خانه بهداشت', 'description' => null],
            ['title' => 'کارگزینی', 'description' => 'واحد اداری'],
        ];

        foreach ($types as $type) {
            UnitType::firstOrCreate(
                ['title' => $type['title']],
                ['description' => $type['description']]
            );
        }
    }
}
