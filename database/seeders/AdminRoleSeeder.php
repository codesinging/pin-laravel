<?php

namespace Database\Seeders;

use App\Models\AdminRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdminRole::creates(['name' => '超级管理员', 'description' => '拥有系统的全部权限']);
        AdminRole::creates(['name' => '内容管理员', 'description' => '拥有管理内容的权限']);
    }
}
