<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdminUser::creates([
            'username' => 'admin',
            'name' => 'Admin',
            'password' => 'admin.123',
            'status' => true,
        ]);

        AdminUser::factory()->count(5)->create();
    }
}