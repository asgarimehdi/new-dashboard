<?php

namespace Database\Seeders;
use App\Models\UnitType;
use Illuminate\Database\Seeder;

class UnitTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // سطح ملی
            ['title' => 'وزارت بهداشت', 'description' => 'بالاترین سطح سازمانی'],

            // سطح دانشگاه
            ['title' => 'دانشگاه علوم پزشکی', 'description' => 'سطح استانی / شهری دانشگاه'],

            // معاونت‌ها
            ['title' => 'معاونت بهداشت', 'description' => null],
            ['title' => 'معاونت درمان', 'description' => null],
            ['title' => 'معاونت آموزش', 'description' => null],

            // شهرستان
            ['title' => 'شبکه بهداشت و درمان', 'description' => null],
            ['title' => 'مرکز بهداشت شهرستان', 'description' => null],

            // مراکز خدمات
            ['title' => 'مرکز خدمات جامع سلامت شهری', 'description' => null],
            ['title' => 'مرکز خدمات جامع سلامت روستایی', 'description' => null],
            ['title' => 'مرکز خدمات جامع سلامت شهری روستایی', 'description' => null],

            // سطوح پایه
            ['title' => 'پایگاه سلامت', 'description' => null],
            ['title' => 'خانه بهداشت', 'description' => null],

            // واحدهای اداری
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
