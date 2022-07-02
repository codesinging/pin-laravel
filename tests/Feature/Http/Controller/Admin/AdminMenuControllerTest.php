<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Models\AdminMenu;
use App\Models\AdminPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class AdminMenuControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testIndex()
    {
        $page1 = AdminPage::factory()->create(['name' => '首页', 'path' => '/home']);
        $page2 = AdminPage::factory()->create(['name' => '页面管理', 'path' => '/admin_pages']);
        $page3 = AdminPage::factory()->create(['name' => '菜单管理', 'path' => '/admin_menus']);

        $menus = [
            ['name' => '首页', 'page_id' => $page1['id']],
            ['name' => '系统管理', 'children' => [
                ['name' => '页面管理', 'page_id' => $page2['id']],
                ['name' => '菜单管理', 'page_id' => $page3['id'], 'public' => true],
            ]],
        ];

        foreach ($menus as $menu) {
            AdminMenu::create($menu);
        }

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_menus')
            ->assertJsonPath('data.0.name', '首页')
            ->assertJsonPath('data.0.page_id', $page1['id'])
            ->assertJsonPath('data.0.permission.permissionable_type', AdminMenu::class)
            ->assertJsonPath('data.0.page.id', $page1['id'])
            ->assertJsonCount(2, 'data.1.children')
            ->assertJsonPath('data.1.children.0.name', '页面管理')
            ->assertJsonPath('data.1.children.0.page_id', $page2['id'])
            ->assertJsonPath('data.1.children.0.page.id', $page2['id'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_menus?public=0')
            ->assertJsonPath('data.0.name', '首页')
            ->assertJsonPath('data.0.page_id', $page1['id'])
            ->assertJsonPath('data.0.permission.permissionable_type', AdminMenu::class)
            ->assertJsonPath('data.0.page.id', $page1['id'])
            ->assertJsonCount(1, 'data.1.children')
            ->assertJsonPath('data.1.children.0.name', '页面管理')
            ->assertJsonPath('data.1.children.0.page_id', $page2['id'])
            ->assertJsonPath('data.1.children.0.page.id', $page2['id'])
            ->assertJsonPath('code', 0)
            ->assertOk();
    }

    public function testStore()
    {
        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_menus')
            ->assertStatus(422);

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_menus', ['name' => 'menu_1', 'page_id' => 1])
            ->assertJsonPath('data.name', 'menu_1')
            ->assertJsonPath('data.page_id', 1)
            ->assertJsonPath('data.parent_id', null)
            ->assertOk();

        $parent = AdminMenu::factory()->create();

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_menus', ['name' => 'menu_1', 'page_id' => 1, 'parent_id' => $parent['id']])
            ->assertJsonPath('data.name', 'menu_1')
            ->assertJsonPath('data.page_id', 1)
            ->assertJsonPath('data.parent_id', $parent['id'])
            ->assertOk();

        $menus = AdminMenu::all()->toTree()->toArray();

        self::assertEquals('menu_1', $menus[1]['children'][0]['name']);
    }

    public function testUpdate()
    {
        $page1 = AdminPage::factory()->create(['name' => '首页', 'path' => '/home']);
        $page2 = AdminPage::factory()->create(['name' => '页面管理', 'path' => '/admin_pages']);
        $page3 = AdminPage::factory()->create(['name' => '菜单管理', 'path' => '/admin_menus']);

        $menu1 = AdminMenu::create(['name' => '首页', 'page_id' => $page1['id']]);
        $menu2 = AdminMenu::create(['name' => '系统管理']);
        $menu3 = AdminMenu::create(['name' => '页面管理', 'page_id' => $page2['id']], $menu2);
        $menu4 = AdminMenu::create(['name' => '菜单管理', 'page_id' => $page3['id']], $menu2);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_menus/' . $menu1['id'], ['name' => '首页1', 'page_id' => $page1['id']])
            ->assertJsonPath('data.name', '首页1')
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_menus/' . $menu3['id'], ['name' => '页面管理1', 'page_id' => $page2['id'], 'parent_id' => $menu2['id']])
            ->assertJsonPath('data.name', '页面管理1')
            ->assertJsonPath('data.parent_id', $menu2['id'])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_menus/' . $menu3['id'], ['name' => '页面管理2', 'page_id' => $page2['id']])
            ->assertJsonPath('data.name', '页面管理2')
            ->assertJsonPath('data.parent_id', null)
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_menus/' . $menu4['id'], ['name' => '菜单管理1', 'page_id' => $page3['id'], 'parent_id' => $menu1['id']])
            ->assertJsonPath('data.name', '菜单管理1')
            ->assertJsonPath('data.parent_id', $menu1['id'])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_menus/' . $menu2['id'], ['name' => '系统管理2', 'parent_id' => $menu4['id']])
            ->assertJsonPath('data.name', '系统管理2')
            ->assertJsonPath('data.parent_id', $menu4['id'])
            ->assertOk();

        $menus = AdminMenu::all()->toTree()->toArray();

        self::assertCount(2, $menus);
        self::assertEquals($menu1['id'], $menus[0]['id']);
        self::assertEquals($menu3['id'], $menus[1]['id']);

        self::assertCount(1, $menus[0]['children']);
        self::assertEquals($menu4['id'], $menus[0]['children'][0]['id']);
        self::assertEquals($menu1['id'], $menus[0]['children'][0]['parent_id']);

        self::assertCount(1, $menus[0]['children'][0]['children']);
        self::assertEquals($menu2['id'], $menus[0]['children'][0]['children'][0]['id']);
        self::assertEquals($menu4['id'], $menus[0]['children'][0]['children'][0]['parent_id']);
    }

    public function testShow()
    {
        $menu = AdminMenu::factory()->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_menus/' . $menu['id'])
            ->assertJsonPath('data.id', $menu['id'])
            ->assertOk();
    }

    public function testDestroy()
    {
        $menu = AdminMenu::factory()->create();

        $this->actingAsSuperAdminUser()
            ->deleteJson('api/admin/admin_menus/' . $menu['id'])
            ->assertOk();

        $this->assertModelMissing($menu);
    }
}
