<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $unit = Unit::first(); // فعلاً اولین واحد

        User::firstOrCreate(
            ['national_code' => '4400111222'],
            [
                'full_name' => 'صادق بیگلر',
                'unit_id' => $unit?->id,
                'is_active' => true,
            ]
        );
    }
}
