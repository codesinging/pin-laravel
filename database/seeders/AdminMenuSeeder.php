<?php

namespace Database\Seeders;

use App\Models\AdminMenu;
use App\Models\AdminPage;
use Illuminate\Database\Seeder;

class AdminMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->menus() as $menu) {
            AdminMenu::create($menu);
        }
    }

    private function menus(): array
    {
        return [
            ['name' => '首页', 'page_id' => $this->pageId('/home'), 'icon' => 'bi-house', 'public' => true, 'default' => true, 'sort' => 9],
            ['name' => '系统管理', 'icon' => 'bi-command', 'opened' => true, 'sort' => 3, 'children' => [
                ['name' => '页面管理', 'page_id' => $this->pageId('/admin_pages'), 'icon' => 'bi-file-earmark-text', 'sort' => 99],
                ['name' => '菜单管理', 'page_id' => $this->pageId('/admin_menus'), 'icon' => 'bi-list', 'sort' => 98],
                ['name' => '角色管理', 'page_id' => $this->pageId('/admin_roles'), 'icon' => 'bi-people', 'sort' => 97],
                ['name' => '管理员管理', 'page_id' => $this->pageId('/admin_users'), 'icon' => 'bi-person', 'sort' => 96],
                ['name' => '动作管理', 'page_id' => $this->pageId('/admin_actions'), 'icon' => 'bi-shield-check', 'sort' => 95],
                ['name' => '操作日志管理', 'page_id' => $this->pageId('/admin_logs'), 'icon' => 'bi-file-earmark-text', 'sort' => 94],
            ]],
        ];
    }

    private function pageId(string $path)
    {
        return AdminPage::wheres('path', $path)->value('id');
    }
}
