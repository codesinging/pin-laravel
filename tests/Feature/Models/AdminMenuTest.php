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

    public function testIsPublic()
    {
        /** @var AdminMenu $menu1 */
        $menu1 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu2 */
        $menu2 = AdminMenu::factory()->create(['public' => true]);

        self::assertFalse($menu1->isPublic());
        self::assertTrue($menu2->isPublic());
    }

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
        /** @var AdminPage $page1 */
        $page1 = AdminPage::factory()->create();
        /** @var AdminPage $page2 */
        $page2 = AdminPage::factory()->create(['public' => true]);

        /** @var AdminMenu $menu1 */
        $menu1 = AdminMenu::factory()->create(['page_id' => $page1['id']]);

        /** @var AdminMenu $menu2 */
        $menu2 = AdminMenu::factory()->create(['page_id' => $page2['id'], 'public' => true]);

        $this->assertModelExists($menu1);
        $this->assertModelExists($menu2);

        $this->assertDatabaseHas(AdminPermission::class, [
            'permissionable_id' => $menu1['id'],
            'permissionable_type' => $menu1::class,
            'guard_name' => $menu1->guard_name,
        ]);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $menu2['id'],
            'permissionable_type' => $menu2::class,
            'guard_name' => $menu2->guard_name,
        ]);

        $menu1->delete();
        $menu2->delete();

        $this->assertModelMissing($menu1);
        $this->assertModelMissing($menu2);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $menu1['id'],
            'permissionable_type' => $menu1::class,
            'guard_name' => $menu1->guard_name,
        ]);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $menu2['id'],
            'permissionable_type' => $menu2::class,
            'guard_name' => $menu2->guard_name,
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
