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

class AdminPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function testPermissionable()
    {
        $adminPage = AdminPage::factory()->create();

        $permission = AdminPermission::firsts();

        self::assertEquals($adminPage['id'], $permission->permissionable['id']);
        self::assertEquals($adminPage::class, $permission->permissionable::class);
    }
}
