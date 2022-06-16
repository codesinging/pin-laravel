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

    public function testWithPermission()
    {
        AdminPage::factory()->create();

        $page = AdminPage::firsts();

        self::assertArrayHasKey('permission', $page->toArray());

        self::assertEquals($page['id'], $page['permission']['permissionable_id']);
        self::assertEquals($page::class, $page['permission']['permissionable_type']);
    }

    public function testCreatedAndDeletedEvent()
    {
        /** @var AdminPage $page */
        $page = AdminPage::factory()->create();

        $this->assertModelExists($page);

        $this->assertDatabaseHas(AdminPermission::class, [
            'permissionable_id' => $page['id'],
            'permissionable_type' => $page::class,
            'guard_name' => $page->guard_name,
        ]);

        $page->delete();

        $this->assertModelMissing($page);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $page['id'],
            'permissionable_type' => $page::class,
            'guard_name' => $page->guard_name,
        ]);
    }
}
