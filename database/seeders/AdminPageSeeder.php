<?php

namespace Database\Seeders;

use App\Models\AdminPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminPageSeeder extends Seeder
{
    protected array $pages = [
        ['name' => '首页', 'path' => '/home'],
        ['name' => '页面管理', 'path' => '/admin_pages'],
        ['name' => '菜单管理', 'path' => '/admin_menus'],
        ['name' => '角色管理', 'path' => '/admin_roles'],
        ['name' => '管理员管理', 'path' => '/admin_users'],
        ['name' => '动作管理', 'path' => '/admin_actions'],
        ['name' => '修改密码', 'path' => '/auth_password'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->pages as $page) {
            AdminPage::creates($page);
        }
    }
}
