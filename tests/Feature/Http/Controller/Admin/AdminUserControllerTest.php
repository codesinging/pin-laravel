<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Exceptions\Errors;
use App\Models\AdminUser;
use App\Models\AdminPermission;
use App\Models\AdminRole;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testIndex()
    {
        /** @var AdminUser $admin1 */
        $admin1 = AdminUser::factory()->create(['username' => 'admin1', 'super' => true]);

        /** @var AdminUser $admin2 */
        $admin2 = AdminUser::factory()->create(['username' => 'admin2']);

        /** @var AdminUser $admin3 */
        $admin3 = AdminUser::factory()->create(['username' => 'admin3']);

        /** @var AdminUser $admin4 */
        $admin4 = AdminUser::factory()->create(['username' => 'admin4']);

        /** @var AdminRole $role1 */
        $role1 = AdminRole::factory()->create(['name' => 'role1']);

        /** @var AdminRole $role2 */
        $role2 = AdminRole::factory()->create(['name' => 'role2']);

        /** @var AdminRole $role3 */
        $role3 = AdminRole::factory()->create(['name' => 'role3']);

        /** @var AdminRole $role4 */
        $role4 = AdminRole::factory()->create(['name' => 'role4']);

        $admin1->assignRole($role1);
        $admin2->assignRole($role2);
        $admin3->assignRole([$role1, $role2]);
        $admin4->assignRole($role4);

        $this->actingAs($admin1)
            ->getJson('api/admin/admin_users')
            ->assertJsonCount(4, 'data')
            ->assertJsonCount(1, 'data.0.roles')
            ->assertOk();

        $this->actingAs($admin1)
            ->getJson('api/admin/admin_users?role=' . $role1['id'])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.*.id', [$admin1['id'], $admin3['id']])
            ->assertOk();

        $this->actingAs($admin1)
            ->getJson('api/admin/admin_users?role=' . $role4['id'])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.*.id', [$admin4['id']])
            ->assertOk();

        $this->actingAs($admin1)
            ->getJson('api/admin/admin_users?role=' . $role3['id'])
            ->assertJsonCount(0, 'data')
            ->assertOk();
    }

    public function testStore()
    {
        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_users')
            ->assertJsonStructure(['errors' => ['username', 'name']])
            ->assertStatus(422);

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_users', ['username' => 'username', 'name' => 'name'])
            ->assertJsonStructure(['errors' => ['password']])
            ->assertStatus(422);

        AdminUser::factory()->create(['username' => 'username', 'name' => 'name']);

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_users', ['username' => 'username', 'name' => 'name', 'password' => 'admin'])
            ->assertJsonStructure(['errors' => ['username', 'name']])
            ->assertStatus(422);

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_users', ['username' => 'username1', 'name' => 'name1', 'password' => 'admin'])
            ->assertJsonPath('data.username', 'username1')
            ->assertOk();

        $this->assertDatabaseHas(AdminUser::class, ['username' => 'username1']);
    }

    public function testUpdate()
    {
        $admin1 = AdminUser::factory()->create(['username' => 'admin1']);
        $admin2 = AdminUser::factory()->create(['username' => 'admin2', 'super' => true]);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin1['id'])
            ->assertJsonStructure(['errors' => ['username', 'name']])
            ->assertStatus(422);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin1['id'], ['username' => 'username'])
            ->assertJsonStructure(['errors' => ['name']])
            ->assertStatus(422);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin1['id'], ['username' => 'admin2', 'name' => 'name'])
            ->assertJsonStructure(['errors' => ['username']])
            ->assertJsonValidationErrors(['username' => ['登录账号 已经存在。']])
            ->assertStatus(422);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin2['id'], ['username' => 'username', 'name' => 'name'])
            ->assertJsonPath('code', Errors::Forbidden())
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin1['id'], ['username' => 'username', 'name' => 'name'])
            ->assertJsonPath('data.username', 'username')
            ->assertJsonPath('data.name', 'name')
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin1['id'], ['username' => 'username', 'name' => 'name', 'password' => 'pass'])
            ->assertOk();

        $admin1->refresh();

        self::assertTrue(Hash::check('pass', $admin1['password']));
    }

    public function testShow()
    {
        $admin = AdminUser::factory()->create(['username' => 'username']);

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $admin['id'])
            ->assertJsonPath('data.username', 'username')
            ->assertOk();
    }

    public function testDestroy()
    {
        $admin1 = AdminUser::factory()->create(['username' => 'admin1']);
        $admin2 = AdminUser::factory()->create(['username' => 'admin2', 'super' => true]);

        $this->actingAsSuperAdminUser()
            ->deleteJson('api/admin/admin_users/' . $admin2['id'])
            ->assertJsonPath('code', Errors::Forbidden())
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->deleteJson('api/admin/admin_users/' . $admin1['id'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->assertModelMissing($admin1);
        $this->assertModelExists($admin2);
    }

    /**
     * @throws Exception
     */
    public function testPermit()
    {
        AdminPermission::create(['name' => 'permission1']);
        AdminPermission::create(['name' => 'permission2']);
        AdminPermission::create(['name' => 'permission3']);
        AdminPermission::create(['name' => 'permission4']);

        /** @var AdminUser $admin */
        $admin = AdminUser::factory()->create();

        self::assertFalse($admin->hasAnyPermission(['permission1', 'permission2', 'permission3', 'permission4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/permit', ['permissions' => 'permission1'])
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllPermissions(['permission1']));
        self::assertFalse($admin->hasAnyPermission(['permission2', 'permission3', 'permission4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/permit', ['permissions' => ['permission1', 'permission3']])
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllPermissions(['permission1', 'permission3']));
        self::assertFalse($admin->hasAnyPermission(['permission2', 'permission4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/permit', ['permissions' => ['permission2', 'permission3', 'permission4']])
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllPermissions(['permission2', 'permission3', 'permission4']));
        self::assertFalse($admin->hasAnyPermission(['permission1']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/permit')
            ->assertOk();

        $admin->refresh();

        self::assertFalse($admin->hasAnyPermission(['permission1', 'permission2', 'permission3', 'permission4']));
    }

    public function testAssign()
    {
        AdminRole::factory()->create(['name' => 'role1']);
        AdminRole::factory()->create(['name' => 'role2']);
        AdminRole::factory()->create(['name' => 'role3']);
        AdminRole::factory()->create(['name' => 'role4']);

        /** @var AdminUser $admin */
        $admin = AdminUser::factory()->create();

        self::assertFalse($admin->hasAnyRole(['role1', 'role2', 'role3', 'role4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/assign', ['roles' => 'role1'])
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllRoles(['role1']));
        self::assertFalse($admin->hasAnyRole(['role2', 'role3', 'role4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/assign', ['roles' => ['role1', 'role3']])
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllRoles(['role1', 'role3']));
        self::assertFalse($admin->hasAnyRole(['role2', 'role4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/assign', ['roles' => ['role2', 'role3']])
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllRoles(['role2', 'role3']));
        self::assertFalse($admin->hasAnyRole(['role1', 'role4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/assign')
            ->assertOk();

        $admin->refresh();

        self::assertFalse($admin->hasAnyRole(['role1', 'role2', 'role3', 'role4']));
    }
}
