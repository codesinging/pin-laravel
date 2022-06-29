<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Models;

use App\Models\AdminPage;
use App\Models\AdminPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPageTest extends TestCase
{
    use RefreshDatabase;

    public function testPermissionAttribute()
    {
        /** @var AdminPage $page */
        $page = AdminPage::factory()->create();

        self::assertEquals($page['id'], $page['permission']['permissionable_id']);
        self::assertEquals($page::class, $page['permission']['permissionable_type']);
    }

    public function testIsPublic()
    {
        /** @var AdminPage $page1 */
        $page1 = AdminPage::factory()->create();

        /** @var AdminPage $page2 */
        $page2 = AdminPage::factory()->create(['public' => true]);

        self::assertFalse($page1->isPublic());
        self::assertTrue($page2->isPublic());
    }

    public function testWithPermission()
    {
        AdminPage::factory()->create();

        $page = AdminPage::instance()->with('permission')->first();

        self::assertArrayHasKey('permission', $page->toArray());

        self::assertEquals($page['id'], $page['permission']['permissionable_id']);
        self::assertEquals($page::class, $page['permission']['permissionable_type']);
    }

    public function testCreatedAndDeletedEvent()
    {
        /** @var AdminPage $page1 */
        $page1 = AdminPage::factory()->create();

        /** @var AdminPage $page2 */
        $page2 = AdminPage::factory()->create(['public' => true]);

        $this->assertModelExists($page1);
        $this->assertModelExists($page2);

        $this->assertDatabaseHas(AdminPermission::class, [
            'permissionable_id' => $page1['id'],
            'permissionable_type' => $page1::class,
            'guard_name' => $page1->guard_name,
        ]);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $page2['id'],
            'permissionable_type' => $page2::class,
            'guard_name' => $page2->guard_name,
        ]);

        $page1->delete();
        $page2->delete();

        $this->assertModelMissing($page1);
        $this->assertModelMissing($page2);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $page1['id'],
            'permissionable_type' => $page1::class,
            'guard_name' => $page1->guard_name,
        ]);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $page2['id'],
            'permissionable_type' => $page2::class,
            'guard_name' => $page2->guard_name,
        ]);
    }
}
