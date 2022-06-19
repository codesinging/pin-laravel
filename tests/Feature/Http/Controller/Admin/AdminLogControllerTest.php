<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Models\AdminLog;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class AdminLogControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testIndex()
    {
        AdminUser::factory()->count(5)->create();
        AdminLog::factory()->count(10)->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_logs')
            ->assertJsonPath('code', 0)
            ->assertOk();
    }

    public function testShow()
    {
        $user = AdminUser::factory()->create();
        $log = AdminLog::factory()->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_logs/' . $log['id'])
            ->assertJsonPath('data.id', $log['id'])
            ->assertJsonPath('data.user_id', $user['id'])
            ->assertOk();
    }
}
