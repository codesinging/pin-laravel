<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Models;

use App\Models\AdminMenu;
use App\Models\AdminPage;
use App\Models\AdminPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMenuTest extends TestCase
{
    use RefreshDatabase;

    public function testPermissionAttribute()
    {
        /** @var AdminPage $page */
        $page = AdminPage::factory()->create();

        /** @var AdminMenu $menu */
        $menu = AdminMenu::factory()->create(['page_id' => $page['id']]);

        self::assertEquals($menu['id'], $menu['permission']['permissionable_id']);
        self::assertEquals($menu::class, $menu['permission']['permissionable_type']);
    }

    public function testWithPermission()
    {
        $page = AdminPage::factory()->create();

        AdminMenu::factory()->create(['page_id' => $page['id']]);

        $menu = AdminMenu::instance()->with('permission')->first();

        self::assertArrayHasKey('permission', $menu->toArray());

        self::assertEquals($menu['id'], $menu['permission']['permissionable_id']);
        self::assertEquals($menu::class, $menu['permission']['permissionable_type']);
    }

    public function testCreatedAndDeletedEvent()
    {
        /** @var AdminPage $page */
        $page = AdminPage::factory()->create();

        /** @var AdminMenu $menu */
        $menu = AdminMenu::factory()->create(['page_id' => $page['id']]);

        $this->assertModelExists($menu);

        $this->assertDatabaseHas(AdminPermission::class, [
            'permissionable_id' => $menu['id'],
            'permissionable_type' => $menu::class,
            'guard_name' => $menu->guard_name,
        ]);

        $menu->delete();

        $this->assertModelMissing($menu);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $menu['id'],
            'permissionable_type' => $menu::class,
            'guard_name' => $menu->guard_name,
        ]);
    }

    public function testPageAttribute()
    {
        /** @var AdminPage $page */
        $page = AdminPage::factory()->create();

        /** @var AdminMenu $menu */
        $menu = AdminMenu::factory()->create(['page_id' => $page['id']]);

        self::assertEquals($page['id'], $menu['page']['id']);
    }

    public function testWithPage()
    {
        $page = AdminPage::factory()->create();

        AdminMenu::factory()->create(['page_id' => $page['id']]);

        $menu = AdminMenu::firsts();

        self::assertArrayHasKey('page', $menu->toArray());

        self::assertEquals($page['id'], $menu['page']['id']);
    }
}
