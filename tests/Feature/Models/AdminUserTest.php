<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Models;

use App\Models\AdminLogin;
use App\Models\AdminMenu;
use App\Models\AdminPage;
use App\Models\AdminUser;
use App\Models\AdminRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function testPasswordAttribute()
    {
        $admin = AdminUser::creates([
            'username' => 'admin',
            'name' => 'Admin',
            'password' => 'admin.123',
        ]);

        self::assertTrue(Hash::check('admin.123', $admin['password']));
        self::assertFalse(Hash::check('admin.111', $admin['password']));

        $admin->fill(['password' => 'admin.111'])->save();

        self::assertTrue(Hash::check('admin.111', $admin['password']));
        self::assertFalse(Hash::check('admin.123', $admin['password']));

        $admin->fill(['name' => 'admin_name'])->save();

        self::assertTrue(Hash::check('admin.111', $admin['password']));
        self::assertEquals('admin_name', $admin['name']);

        $admin->update(['password' => 'admin.222']);

        self::assertTrue(Hash::check('admin.222', $admin['password']));

        $admin->password = 'admin.333';
        $admin->save();

        self::assertTrue(Hash::check('admin.333', $admin['password']));

        $admin['password'] = 'admin.444';
        $admin->save();

        self::assertTrue(Hash::check('admin.444', $admin['password']));
    }

    public function testLogin()
    {
        /** @var AdminUser $admin */
        $admin = AdminUser::factory()->create();

        $ip = request()->ip();

        $admin->login($ip, false, 1, 'error');
        $admin->login($ip, true, 0, 'success');

        $this->assertDatabaseCount(AdminLogin::class, 2);

        $this->assertDatabaseHas(AdminLogin::class, [
            'user_id' => $admin['id'],
            'ip' => $ip,
            'result' => false,
            'code' => 1,
            'message' => 'error',
        ]);

        $this->assertDatabaseHas(AdminLogin::class, [
            'user_id' => $admin['id'],
            'ip' => $ip,
            'result' => true,
            'code' => 0,
            'message' => 'success',
        ]);
    }

    public function testRelationOfRoles()
    {
        /** @var AdminUser $admin */
        $admin = AdminUser::factory()->create();

        $role1 = AdminRole::factory()->create(['name' => 'role1']);
        $role2 = AdminRole::factory()->create(['name' => 'role2']);

        $admin->assignRole([$role1, $role2]);

        self::assertArrayHasKey('roles', $admin->toArray());
        self::assertCount(2, $admin['roles']);
        self::assertEquals('role1', $admin['roles'][0]['name']);

        $admins = AdminUser::all()->toArray();

        self::assertArrayHasKey('roles', $admins[0]);
        self::assertCount(2, $admins[0]['roles']);
        self::assertEquals('role1', $admins[0]['roles'][0]['name']);
    }

    public function testGetPermissionsViaRoles()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        /** @var AdminRole $role1 */
        $role1 = AdminRole::factory()->create();

        /** @var AdminRole $role2 */
        $role2 = AdminRole::factory()->create(['status' => false]);

        /** @var AdminMenu $menu1 */
        $menu1 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu2 */
        $menu2 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu3 */
        $menu3 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu4 */
        $menu4 = AdminMenu::factory()->create();

        $role1->givePermissionTo($menu1->permission, $menu2->permission);
        $role2->givePermissionTo($menu1->permission, $menu2->permission);

        $user1->givePermissionTo($menu3->permission);
        $user2->givePermissionTo($menu4->permission);

        $user1->assignRole($role1);
        $user2->assignRole($role2);

        self::assertCount(3, $user1->getAllPermissions());
        self::assertCount(1, $user2->getAllPermissions());
    }

    public function testPermissionables()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        /** @var AdminUser $user3 */
        $user3 = AdminUser::factory()->create();

        /** @var AdminUser $user4 */
        $user4 = AdminUser::factory()->create();

        /** @var AdminUser $user5 */
        $user5 = AdminUser::factory()->create();

        /** @var AdminRole $role1 */
        $role1 = AdminRole::factory()->create();

        /** @var AdminRole $role2 */
        $role2 = AdminRole::factory()->create();

        /** @var AdminPage $page1 */
        $page1 = AdminPage::factory()->create(['status' => false]);

        /** @var AdminPage $page2 */
        $page2 = AdminPage::factory()->create();

        /** @var AdminPage $page3 */
        $page3 = AdminPage::factory()->create(['public' => true]);

        /** @var AdminMenu $menu1 */
        $menu1 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu2 */
        $menu2 = AdminMenu::factory()->create();

        $user1->givePermissionTo($page1->permission, $menu1->permission);
        $user2->givePermissionTo($menu1->permission, $menu2->permission);
        $user3->givePermissionTo($page1->permission, $page2->permission, $menu1->permission);

        $role1->givePermissionTo($menu1->permission);
        $role2->givePermissionTo($page1->permission);

        $user5->assignRole($role1, $role2);
        $user5->givePermissionTo($menu2->permission);

        self::assertCount(1, $user1->permissionables());
        self::assertCount(0, $user1->permissionables(AdminPage::class));
        self::assertCount(1, $user1->permissionables(AdminMenu::class));

        self::assertTrue($menu1->is($user1->permissionables(AdminMenu::class)[0]));

        self::assertCount(2, $user2->permissionables());
        self::assertCount(0, $user2->permissionables(AdminPage::class));
        self::assertCount(2, $user2->permissionables(AdminMenu::class));

        self::assertTrue($menu1->is($user2->permissionables(AdminMenu::class)[0]));
        self::assertTrue($menu2->is($user2->permissionables(AdminMenu::class)[1]));

        self::assertCount(2, $user3->permissionables());
        self::assertCount(1, $user3->permissionables(AdminPage::class));
        self::assertCount(1, $user3->permissionables(AdminMenu::class));

        self::assertCount(0, $user4->permissionables());
        self::assertCount(0, $user4->permissionables(AdminPage::class));
        self::assertCount(0, $user4->permissionables(AdminMenu::class));

        self::assertCount(2, $user5->permissionables());
        self::assertCount(0, $user5->permissionables(AdminPage::class));
        self::assertCount(2, $user5->permissionables(AdminMenu::class));

        $role1->update(['status' => false]);
        $user5->refresh();

        self::assertCount(1, $user5->permissionables());
        self::assertCount(0, $user5->permissionables(AdminPage::class));
        self::assertCount(1, $user5->permissionables(AdminMenu::class));
    }
}
