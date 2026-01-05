<?php
namespace Database\Seeders;

use App\Models\UnitType;
use App\Models\UnitTypeHierarchy;
use Illuminate\Database\Seeder;

class UnitTypeHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'وزارت بهداشت' => ['دانشگاه علوم پزشکی'],
            'دانشگاه علوم پزشکی' => [
                'معاونت بهداشت', 'معاونت درمان', 'معاونت آموزش',
                'معاونت غذا و دارو', 'معاونت دانشجویی', 'معاونت تحقیقات و فناوری', 'معاونت توسعه مدیریت و منابع'
            ],
            'معاونت بهداشت' => ['شبکه بهداشت و درمان', 'مرکز بهداشت استان'],
            'شبکه بهداشت و درمان' => ['ستاد شبکه بهداشت و درمان', 'مرکز بهداشت شهرستان'],
            'ستاد شبکه بهداشت و درمان' => ['واحد اجرایی ستاد'],
            'مرکز بهداشت شهرستان' => ['واحد فنی مرکز بهداشت', 'واحد محیطی مرکز بهداشت'],
            'واحد محیطی مرکز بهداشت' => [
                'مرکز خدمات جامع سلامت شهری', 
                'مرکز خدمات جامع سلامت روستایی', 
                'مرکز خدمات جامع سلامت شهری روستایی'
            ],
            'مرکز خدمات جامع سلامت شهری' => ['پایگاه سلامت ضمیمه', 'پایگاه سلامت غیر ضمیمه'],
            'مرکز خدمات جامع سلامت روستایی' => ['خانه بهداشت'],
            'مرکز خدمات جامع سلامت شهری روستایی' => ['خانه بهداشت', 'پایگاه سلامت ضمیمه', 'پایگاه سلامت غیر ضمیمه'],
        ];

        foreach ($map as $parent => $children) {
            $parentType = UnitType::where('title', $parent)->first();
            if (!$parentType) continue;

            foreach ($children as $child) {
                $childType = UnitType::where('title', $child)->first();
                if ($childType) {
                    UnitTypeHierarchy::firstOrCreate([
                        'parent_unit_type_id' => $parentType->id,
                        'child_unit_type_id' => $childType->id,
                    ]);
                }
            }
        }
    }
}