<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'id' => 1,
            'name' => 'Admin Guru', // 更新してください
        ]);

        Role::create([
            'id' => 2,
            'name' => 'Staff',
        ]);

    }
}
