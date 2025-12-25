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
                'معاونت بهداشت',
                'معاونت درمان',
                'معاونت آموزش',
            ],
            'معاونت بهداشت' => ['شبکه بهداشت و درمان'],
            'شبکه بهداشت و درمان' => ['مرکز بهداشت شهرستان'],
            'مرکز بهداشت شهرستان' => [
                'مرکز خدمات جامع سلامت شهری',
                'مرکز خدمات جامع سلامت روستایی',
                'مرکز خدمات جامع سلامت شهری روستایی',
            ],
            'مرکز خدمات جامع سلامت شهری' => ['پایگاه سلامت'],
            'مرکز خدمات جامع سلامت شهری روستایی' => ['خانه بهداشت'],
            'مرکز خدمات جامع سلامت روستایی' => ['خانه بهداشت'],
        ];

        foreach ($map as $parent => $children) {
            $parentType = UnitType::where('title', $parent)->first();

            foreach ($children as $child) {
                $childType = UnitType::where('title', $child)->first();

                if ($parentType && $childType) {
                    UnitTypeHierarchy::firstOrCreate([
                        'parent_unit_type_id' => $parentType->id,
                        'child_unit_type_id' => $childType->id,
                    ]);
                }
            }
        }
    }
}
