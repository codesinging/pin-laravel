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
        ['name' => '路由管理', 'path' => '/admin_routes'],
        ['name' => '登录日志管理', 'path' => '/admin_logins'],
        ['name' => '操作日志管理', 'path' => '/admin_logs'],
        ['name' => '个人中心', 'path' => '/user/home', 'public' => true],
        ['name' => '登录日志', 'path' => '/user/logins', 'public' => true],
        ['name' => '操作日志', 'path' => '/user/logs', 'public' => true],
        ['name' => '权限列表', 'path' => '/user/permissions', 'public' => true],
        ['name' => '修改信息', 'path' => '/user/profile', 'public' => true],
        ['name' => '修改密码', 'path' => '/user/password', 'public' => true],
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
