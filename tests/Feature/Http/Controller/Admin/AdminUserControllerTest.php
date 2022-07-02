<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Exceptions\Errors;
use App\Models\AdminAction;
use App\Models\AdminMenu;
use App\Models\AdminPage;
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
        $permission1 = AdminPermission::create(['name' => 'permission1']);
        $permission2 = AdminPermission::create(['name' => 'permission2']);
        $permission3 = AdminPermission::create(['name' => 'permission3']);
        $permission4 = AdminPermission::create(['name' => 'permission4']);

        /** @var AdminUser $admin */
        $admin = AdminUser::factory()->create();

        self::assertFalse($admin->hasAnyPermission(['permission1', 'permission2', 'permission3', 'permission4']));
        self::assertFalse($admin->hasAnyPermission([$permission1['id'], $permission2['id'], $permission3['id'], $permission4['id']]));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/permit', ['permissions' => 'permission1'])
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllPermissions(['permission1']));
        self::assertTrue($admin->hasAllPermissions([$permission1['id']]));
        self::assertFalse($admin->hasAnyPermission(['permission2', 'permission3', 'permission4']));
        self::assertFalse($admin->hasAnyPermission([$permission2['id'], $permission3['id'], $permission4['id']]));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/permit', ['permissions' => ['permission1', 'permission3']])
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllPermissions(['permission1', 'permission3']));
        self::assertFalse($admin->hasAnyPermission(['permission2', 'permission4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/permit', ['permissions' => [$permission2['id'], $permission3['id'], $permission4['id']]])
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllPermissions(['permission2', 'permission3', 'permission4']));
        self::assertTrue($admin->hasAllPermissions([$permission2['id'], $permission3['id'], $permission4['id']]));
        self::assertFalse($admin->hasAnyPermission(['permission1']));
        self::assertFalse($admin->hasAnyPermission([$permission1['id']]));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/permit')
            ->assertOk();

        $admin->refresh();

        self::assertFalse($admin->hasAnyPermission(['permission1', 'permission2', 'permission3', 'permission4']));
    }

    public function testAssign()
    {
        $role1 = AdminRole::factory()->create(['name' => 'role1']);
        $role2 = AdminRole::factory()->create(['name' => 'role2']);
        $role3 = AdminRole::factory()->create(['name' => 'role3']);
        $role4 = AdminRole::factory()->create(['name' => 'role4']);

        /** @var AdminUser $admin */
        $admin = AdminUser::factory()->create();

        self::assertFalse($admin->hasAnyRole(['role1', 'role2', 'role3', 'role4']));
        self::assertFalse($admin->hasAnyRole([$role1['id'], $role2['id'], $role3['id'], $role4['id']]));

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
            ->putJson('api/admin/admin_users/' . $admin['id'] . '/assign', ['roles' => [$role2['id'], $role3['id']]])
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

    public function testPages()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        /** @var AdminUser $user3 */
        $user3 = AdminUser::factory()->create();

        /** @var AdminPage $page1 */
        $page1 = AdminPage::factory()->create();

        /** @var AdminPage $page2 */
        $page2 = AdminPage::factory()->create();

        /** @var AdminPage $page3 */
        $page3 = AdminPage::factory()->create();

        /** @var AdminPage $page4 */
        $page4 = AdminPage::factory()->create(['public' => true]);

        /** @var AdminPage $page5 */
        $page5 = AdminPage::factory()->create(['status' => false]);

        $user1->givePermissionTo($page1->permission);
        $user2->givePermissionTo($page1->permission, $page2->permission, $page5->permission);

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user1['id'] . '/pages')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.*.id', [$page1['id']])
            ->assertJsonPath('data.*.permission.permissionable_id', [$page1['id']])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user2['id'] . '/pages')
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.*.id', [$page1['id'], $page2['id'], $page5['id']])
            ->assertJsonPath('data.*.permission.permissionable_id', [$page1['id'], $page2['id'], $page5['id']])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user3['id'] . '/pages')
            ->assertJsonCount(0, 'data')
            ->assertOk();
    }

    public function testMenus()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        /** @var AdminUser $user3 */
        $user3 = AdminUser::factory()->create();

        /** @var AdminMenu $menu1 */
        $menu1 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu2 */
        $menu2 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu3 */
        $menu3 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu4 */
        $menu4 = AdminMenu::factory()->create(['public' => true]);

        /** @var AdminMenu $menu5 */
        $menu5 = AdminMenu::factory()->create(['status' => false]);

        $user1->givePermissionTo($menu1->permission);
        $user2->givePermissionTo($menu1->permission, $menu2->permission, $menu5->permission);

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user1['id'] . '/menus')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.*.id', [$menu1['id']])
            ->assertJsonPath('data.*.permission.permissionable_id', [$menu1['id']])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user2['id'] . '/menus')
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.*.id', [$menu1['id'], $menu2['id'], $menu5['id']])
            ->assertJsonPath('data.*.permission.permissionable_id', [$menu1['id'], $menu2['id'], $menu5['id']])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user3['id'] . '/menus')
            ->assertJsonCount(0, 'data')
            ->assertOk();
    }

    public function testActions()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        /** @var AdminUser $user3 */
        $user3 = AdminUser::factory()->create();

        /** @var AdminMenu $action1 */
        $action1 = AdminAction::factory()->create();

        /** @var AdminMenu $action2 */
        $action2 = AdminAction::factory()->create();

        /** @var AdminMenu $action3 */
        $action3 = AdminAction::factory()->create();

        $user1->givePermissionTo($action1->permission);
        $user2->givePermissionTo($action1->permission, $action2->permission);

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user1['id'] . '/actions')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.*.id', [$action1['id']])
            ->assertJsonPath('data.*.permission.permissionable_id', [$action1['id']])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user2['id'] . '/actions')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.*.id', [$action1['id'], $action2['id']])
            ->assertJsonPath('data.*.permission.permissionable_id', [$action1['id'], $action2['id']])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user3['id'] . '/actions')
            ->assertJsonCount(0, 'data')
            ->assertOk();
    }

    public function testPermissionables()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        /** @var AdminUser $user3 */
        $user3 = AdminUser::factory()->create();

        /** @var AdminPage $page1 */
        $page1 = AdminPage::factory()->create();

        /** @var AdminPage $page2 */
        $page2 = AdminPage::factory()->create();

        /** @var AdminMenu $menu1 */
        $menu1 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu2 */
        $menu2 = AdminMenu::factory()->create();

        /** @var AdminMenu $action1 */
        $action1 = AdminAction::factory()->create();

        /** @var AdminMenu $action2 */
        $action2 = AdminAction::factory()->create();

        $user1->givePermissionTo($page1->permission, $action1->permission);
        $user2->givePermissionTo($page1->permission, $menu2->permission, $action2->permission);

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user1['id'] . '/permissionables')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.*.id', [$page1['id'], $action1['id']])
            ->assertJsonPath('data.*.permission.permissionable_id', [$page1['id'], $action1['id']])
            ->assertJsonPath('data.*.permission.permissionable_type', [$page1::class, $action1::class])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user2['id'] . '/permissionables')
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.*.id', [$page1['id'], $menu2['id'], $action2['id']])
            ->assertJsonPath('data.*.permission.permissionable_id', [$page1['id'], $menu2['id'], $action2['id']])
            ->assertJsonPath('data.*.permission.permissionable_type', [$page1::class, $menu2::class, $action2::class])
            ->assertOk();
    }

    public function testPermissions()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        /** @var AdminUser $user3 */
        $user3 = AdminUser::factory()->create();

        /** @var AdminUser $user4 */
        $user4 = AdminUser::factory()->create();

        /** @var AdminRole $role1 */
        $role1 = AdminRole::factory()->create();

        /** @var AdminRole $role2 */
        $role2 = AdminRole::factory()->create();

        /** @var AdminPage $page1 */
        $page1 = AdminPage::factory()->create();

        /** @var AdminPage $page2 */
        $page2 = AdminPage::factory()->create();

        /** @var AdminMenu $menu1 */
        $menu1 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu2 */
        $menu2 = AdminMenu::factory()->create();

        /** @var AdminMenu $action1 */
        $action1 = AdminAction::factory()->create();

        /** @var AdminMenu $action2 */
        $action2 = AdminAction::factory()->create();

        $role1->givePermissionTo($page1->permission, $page2->permission);
        $role2->givePermissionTo($menu1->permission, $action1->permission);

        $user1->givePermissionTo($page1->permission, $action1->permission);
        $user2->givePermissionTo($page1->permission, $menu2->permission, $action2->permission);
        $user3->givePermissionTo($page1->permission, $menu2->permission);

        $user3->assignRole($role1, $role2);

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user1['id'] . '/permissions')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.*.permissionable_id', [$page1['id'], $action1['id']])
            ->assertJsonPath('data.*.permissionable_type', [$page1::class, $action1::class])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user2['id'] . '/permissions')
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.*.permissionable_id', [$page1['id'], $menu2['id'], $action2['id']])
            ->assertJsonPath('data.*.permissionable_type', [$page1::class, $menu2::class, $action2::class])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user3['id'] . '/permissions')
            ->assertJsonCount(5, 'data')
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user3['id'] . '/permissions?direct=true')
            ->assertJsonCount(2, 'data')
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_users/' . $user4['id'] . '/permissions')
            ->assertJsonCount(0, 'data')
            ->assertOk();
    }
}
