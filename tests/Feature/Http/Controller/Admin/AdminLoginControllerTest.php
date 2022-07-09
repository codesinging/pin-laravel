<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class AdminLoginControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testIndex()
    {
        /** @var AdminUser $admin */
        $admin = AdminUser::factory()->create();

        $login1 = $admin->login('123', true, 0, 'success');
        $login2 = $admin->login('123', false, 1, 'error');

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_logins')
            ->assertJsonPath('code', 0)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $login2['id'])
            ->assertJsonPath('data.1.id', $login1['id'])
            ->assertJsonPath('data.1.user_id', $admin['id'])
            ->assertJsonPath('data.1.user.id', $admin['id'])
            ->assertOk();
    }

    public function testShow()
    {
        /** @var AdminUser $admin */
        $admin = AdminUser::factory()->create();

        $login1 = $admin->login('123', true, 0, 'success');

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_logins/' . $login1['id'])
            ->assertJsonPath('data.id', $login1['id'])
            ->assertJsonPath('data.user_id', $login1['user_id'])
            ->assertOk();
    }
}
