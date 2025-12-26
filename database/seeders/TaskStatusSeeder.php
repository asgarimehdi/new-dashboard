<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['title' => 'جدید', 'color' => 'gray'],
            ['title' => 'ارجاع شده', 'color' => 'blue'],
            ['title' => 'در حال انجام', 'color' => 'yellow'],
            ['title' => 'انجام شده', 'color' => 'green'],
            ['title' => 'برگشت داده شده', 'color' => 'red'],
        ];

        foreach ($statuses as $status) {
            TaskStatus::firstOrCreate(
                ['title' => $status['title']],
                ['color' => $status['color']]
            );
        }
    }
}
