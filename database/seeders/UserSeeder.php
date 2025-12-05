<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\UserType;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userType = UserType::where('name', 'Cán bộ giảng viên')->first();

        User::updateOrCreate(
            ['username' => 'a'],
            [
                'username' => 'a',
                'password_hash' => bcrypt('123456'),
                'user_type_id' => $userType->id,
                'status' => 'active',
            ]
        );
    }
}
