<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Cán bộ giảng viên', 'description' => 'Giảng viên, cán bộ quản lý'],
            ['name' => 'Sinh viên trong sư phạm', 'description' => 'Sinh viên các ngành sư phạm'],
            ['name' => 'Sinh viên ngoài sư phạm', 'description' => 'Sinh viên các ngành không sư phạm'],
        ];

        foreach ($types as $type) {
            \App\Models\UserType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
