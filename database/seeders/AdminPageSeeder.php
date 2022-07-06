<?php

namespace Database\Seeders;

use App\Models\AdminPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminPageSeeder extends Seeder
{
    protected array $pages = [
        ['name' => '首页', 'path' => '/home', 'public' => true],
        ['name' => '页面管理', 'path' => '/admin_pages'],
        ['name' => '菜单管理', 'path' => '/admin_menus'],
        ['name' => '角色管理', 'path' => '/admin_roles'],
        ['name' => '管理员管理', 'path' => '/admin_users'],
        ['name' => '动作管理', 'path' => '/admin_actions'],
        ['name' => '操作日志管理', 'path' => '/admin_logs'],
        ['name' => '个人中心', 'path' => '/setting/user', 'public' => true],
        ['name' => '账号设置', 'path' => '/setting/account', 'public' => true],
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
