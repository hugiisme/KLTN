<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrgLevel;

class OrgLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['equivalent_name' => 'Trường', 'level_index' => 1],
            ['equivalent_name' => 'Liên chi đoàn', 'level_index' => 2],
            ['equivalent_name' => 'Chi đoàn', 'level_index' => 3],
        ];

        foreach ($levels as $level) {
            OrgLevel::updateOrCreate(
                ['equivalent_name' => $level['equivalent_name']],
                $level
            );
        }
    }
}
