<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activityCategories = [
            ['name' => 'Hoạt động Đoàn', 'is_multi_level' => true],
            ['name' => 'Hoạt động NCKH', 'is_multi_level' => false],
            ['name' => 'Hoạt động NVSP', 'is_multi_level' => false],
        ];

        foreach ($activityCategories as $category) {
            \App\Models\ActivityCategory::create($category);
        }
    }
}
