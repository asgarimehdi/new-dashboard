<?php

namespace Database\Seeders;

use App\Models\UnitType;
use Illuminate\Database\Seeder;

class UnitTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // سطوح عالی و ستادی
            ['title' => 'وزارت بهداشت', 'description' => 'بالاترین سطح سازمانی'],
            ['title' => 'دانشگاه علوم پزشکی', 'description' => 'سطح استانی'],
            ['title' => 'معاونت بهداشت', 'description' => 'زیرمجموعه دانشگاه'],
            ['title' => 'معاونت درمان', 'description' => null],
            ['title' => 'معاونت آموزش', 'description' => null],
            ['title' => 'معاونت غذا و دارو', 'description' => null],
            ['title' => 'معاونت دانشجویی', 'description' => null],
            ['title' => 'معاونت تحقیقات و فناوری', 'description' => null],
            ['title' => 'معاونت توسعه مدیریت و منابع', 'description' => null],
            ['title' => 'مرکز بهداشت استان', 'description' => null],

            // سطوح شبکه و شهرستان
            ['title' => 'شبکه بهداشت و درمان', 'description' => 'سطح شهرستان'],
            ['title' => 'ستاد شبکه بهداشت و درمان', 'description' => null],
            ['title' => 'مرکز بهداشت شهرستان', 'description' => null],
            ['title' => 'حوزه مدیریت', 'description' => null],
            ['title' => 'واحد اجرایی ستاد', 'description' => null],
            ['title' => 'واحد فنی مرکز بهداشت', 'description' => null],
            ['title' => 'واحد محیطی مرکز بهداشت', 'description' => null],

            // واحدهای اداری و پشتیبانی
            ['title' => 'واحد اداری', 'description' => 'شامل روابط عمومی، حراست، دبیرخانه و غیره'],
            ['title' => 'کارگزینی', 'description' => null],
            ['title' => 'امور مالی', 'description' => 'شامل حسابداری، اموال و اسناد'],

            // مراکز خدمات سلامت و سطوح محیطی
            ['title' => 'مرکز خدمات جامع سلامت شهری', 'description' => null],
            ['title' => 'مرکز خدمات جامع سلامت روستایی', 'description' => null],
            ['title' => 'مرکز خدمات جامع سلامت شهری روستایی', 'description' => null],
            ['title' => 'پایگاه سلامت ضمیمه', 'description' => null],
            ['title' => 'پایگاه سلامت غیر ضمیمه', 'description' => null],
            ['title' => 'خانه بهداشت', 'description' => null],
        ];

        foreach ($types as $type) {
            UnitType::firstOrCreate(
                ['title' => $type['title']],
                ['description' => $type['description']]
            );
        }
    }
}