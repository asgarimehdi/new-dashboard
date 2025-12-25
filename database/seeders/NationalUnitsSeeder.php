<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\UnitType;

class NationalUnitsSeeder extends Seeder
{
    public function run(): void
    {
        // پیدا کردن نوع واحد "وزارت بهداشت"
        $ministryType = UnitType::where('title', 'وزارت بهداشت')->first();

        if (!$ministryType) {
            // اگر نوع واحد وجود نداشت، Seeder بی‌سروصدا خارج شود
            // (یعنی ترتیب Seederها درست نیست)
            return;
        }

        // ایجاد یا اطمینان از وجود وزارت بهداشت
        Unit::firstOrCreate(
            [
                'unit_type_id' => $ministryType->id,
                'parent_id'    => null,
            ],
            [
                'name'      => 'وزارت بهداشت',
                'city_id'   => null,   // واحد ملی
                'is_active' => true,
            ]
        );
    }
}
