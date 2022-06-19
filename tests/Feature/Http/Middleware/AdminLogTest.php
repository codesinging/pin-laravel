<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Middleware;

use App\Models\AdminLog;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class AdminLogTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testMiddleware()
    {
        $this->assertDatabaseCount(AdminLog::class, 0);

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/auth/user')
            ->assertOk();

        $this->assertDatabaseCount(AdminLog::class, 0);

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_users', ['username' => 'username', 'name' => 'name', 'password' => '123'])
            ->assertOk();

        $this->assertDatabaseCount(AdminLog::class, 1);

        $user = AdminUser::factory()->create();

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/'. $user['id'], ['username' => 'username1', 'name' => 'name1'])
            ->assertOk();

        $this->assertDatabaseCount(AdminLog::class, 2);

        $this->actingAsSuperAdminUser()
            ->deleteJson('api/admin/admin_users/'. $user['id'])
            ->assertOk();

        $this->assertDatabaseCount(AdminLog::class, 3);
    }
}
