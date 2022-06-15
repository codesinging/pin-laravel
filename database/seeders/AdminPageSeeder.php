<?php

namespace Database\Seeders;

use App\Models\AdminPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminPageSeeder extends Seeder
{
    protected array $pages = [
        ['name' => '首页', 'path' => 'home'],
        ['name' => '菜单管理', 'path' => 'menus'],
        ['name' => '页面管理', 'path' => 'pages'],
        ['name' => '角色管理', 'path' => 'roles'],
        ['name' => '路由管理', 'path' => 'routes'],
        ['name' => '管理员管理', 'path' => 'admins'],
        ['name' => '系统设置', 'path' => 'settings'],
        ['name' => '用户管理', 'path' => 'users'],
        ['name' => '修改密码', 'path' => 'password'],
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
