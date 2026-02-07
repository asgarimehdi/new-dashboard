<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $unit = Unit::first(); // فعلاً اولین واحد

        User::firstOrCreate(
            ['national_code' => '4400176134'],
            [
                'full_name' => 'صادق بیگلر',
                'password' => Hash::make('12345678'),
                'unit_id' => 1,
                'is_active' => true,
            ]
        );
    }
}
