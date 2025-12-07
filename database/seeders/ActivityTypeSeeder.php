<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activityTypes = [
            "Cần phản hồi",
            "Không cần phản hồi"
        ];

        foreach ($activityTypes as $typeName) {
            \App\Models\ActivityType::create(['name' => $typeName]);
        }
    }
}
