<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrgType;


class OrgTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Trường', 'is_exclusive' => true],
            ['name' => 'Liên chi đoàn', 'is_exclusive' => true],
            ['name' => 'Chi đoàn', 'is_exclusive' => true],
            ['name' => 'Câu lạc bộ', 'is_exclusive' => false],
            ['name' => 'Đội', 'is_exclusive' => false],
        ];

        foreach ($types as $type) {
            OrgType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
