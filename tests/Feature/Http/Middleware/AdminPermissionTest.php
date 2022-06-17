<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Middleware;

use App\Exceptions\AdminErrors;
use App\Http\Controllers\Admin\AdminUserController;
use App\Models\AdminAction;
use App\Models\AdminUser;
use Database\Seeders\AdminActionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class AdminPermissionTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testCommonUserVisitNoPermittedRoute()
    {
        /** @var AdminUser $user */
        $user = AdminUser::factory()->create(['super' => false]);

        self::assertFalse($user->isSuper());

        $this->actingAs($user)
            ->getJson('api/admin/auth/user')
            ->assertJsonPath('data.id', $user['id'])
            ->assertOk();
    }

    public function testSuperUserVisitNoPermittedRoute()
    {
        /** @var AdminUser $user */
        $user = AdminUser::factory()->create(['super' => true]);

        self::assertTrue($user->isSuper());

        $this->actingAs($user)
            ->getJson('api/admin/auth/user')
            ->assertJsonPath('data.id', $user['id'])
            ->assertOk();
    }

    public function testCommonUserVisitPermittedRoute()
    {
        $this->seed(AdminActionSeeder::class);

        /** @var AdminUser $user */
        $user = AdminUser::factory()->create(['super' => false]);

        self::assertFalse($user->isSuper());

        $this->actingAs($user)
            ->getJson('api/admin/admin_users')
            ->assertJsonPath('message', AdminErrors::NoPermission->label())
            ->assertStatus(403);
    }

    public function testSuperUserVisitPermittedRoute()
    {
        $this->seed(AdminActionSeeder::class);

        /** @var AdminUser $user */
        $user = AdminUser::factory()->create(['super' => true]);

        self::assertTrue($user->isSuper());

        $this->actingAs($user)
            ->getJson('api/admin/admin_users')
            ->assertOk();
    }

    public function testCommonButHasPermissionUserVisitPermittedRoute()
    {
        $this->seed(AdminActionSeeder::class);

        /** @var AdminUser $user */
        $user = AdminUser::factory()->create(['super' => false]);

        self::assertFalse($user->isSuper());

        $action = AdminAction::findBy(AdminUserController::class.'@index');

        $this->actingAs($user)
            ->getJson('api/admin/admin_users')
            ->assertStatus(403);

        $user->givePermissionTo($action->permission);

        $this->actingAs($user)
            ->getJson('api/admin/admin_users')
            ->assertOk();
    }
}