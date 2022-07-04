<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Models\AdminAction;
use App\Models\AdminMenu;
use App\Models\AdminPage;
use App\Models\AdminPermission;
use App\Models\AdminRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class AdminRoleControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testIndex()
    {
        AdminRole::factory()->count(5)->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_roles')
            ->assertJsonPath('code', 0)
            ->assertJsonCount(5, 'data')
            ->assertOk();
    }

    public function testStore()
    {
        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_roles')
            ->assertJsonStructure(['errors' => ['name']])
            ->assertStatus(422);

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_roles', ['name' => 'role'])
            ->assertJsonPath('data.name', 'role')
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_roles', ['name' => 'role'])
            ->assertJsonStructure(['errors' => ['name']])
            ->assertStatus(422);
    }

    public function testUpdate()
    {
        $role1 = AdminRole::factory()->create(['name' => 'role1']);
        $role2 = AdminRole::factory()->create(['name' => 'role2']);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_roles/' . $role1['id'], ['name' => 'role2'])
            ->assertJsonStructure(['errors' => ['name']])
            ->assertStatus(422);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_roles/' . $role2['id'], ['name' => 'role1'])
            ->assertJsonStructure(['errors' => ['name']])
            ->assertStatus(422);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_roles/' . $role1['id'], ['name' => 'role3'])
            ->assertJsonPath('data.name', 'role3')
            ->assertOk();
    }

    public function testShow()
    {
        $role = AdminRole::factory()->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_roles/' . $role['id'])
            ->assertJsonPath('data.id', $role['id'])
            ->assertOk();
    }

    public function testDestroy()
    {
        $role = AdminRole::factory()->create();

        $this->actingAsSuperAdminUser()
            ->deleteJson('api/admin/admin_roles/' . $role['id'])
            ->assertOk();

        $this->assertModelMissing($role);
    }

    public function testPermit()
    {
        AdminPermission::create(['name' => 'permission1']);
        AdminPermission::create(['name' => 'permission2']);
        AdminPermission::create(['name' => 'permission3']);
        AdminPermission::create(['name' => 'permission4']);

        $role = AdminRole::factory()->create();

        self::assertFalse($role->hasAnyPermission(['permission1', 'permission2', 'permission3', 'permission4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_roles/' . $role['id'] . '/permit', ['permissions' => 'permission1'])
            ->assertOk();

        $role->refresh();

        self::assertTrue($role->hasAllPermissions(['permission1']));
        self::assertFalse($role->hasAnyPermission(['permission2', 'permission3', 'permission4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_roles/' . $role['id'] . '/permit', ['permissions' => ['permission1', 'permission2']])
            ->assertOk();

        $role->refresh();

        self::assertTrue($role->hasAllPermissions(['permission1', 'permission2']));
        self::assertFalse($role->hasAnyPermission(['permission3', 'permission4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_roles/' . $role['id'] . '/permit', ['permissions' => ['permission2', 'permission3']])
            ->assertOk();

        $role->refresh();

        self::assertTrue($role->hasAllPermissions(['permission2', 'permission3']));
        self::assertFalse($role->hasAnyPermission(['permission1', 'permission4']));

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_roles/' . $role['id'] . '/permit')
            ->assertOk();

        $role->refresh();

        self::assertFalse($role->hasAnyPermission(['permission1', 'permission2', 'permission3', 'permission4']));
    }

    public function testPermissions()
    {
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

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_roles/' . $role1['id'] . '/permissions')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.*.permissionable_id', [$page1['id'], $page2['id']])
            ->assertJsonPath('data.*.permissionable_type', [$page1::class, $page2::class])
            ->assertJsonPath('data.*.permissionable.id', [$page1['id'], $page2['id']])
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_roles/' . $role2['id'] . '/permissions')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.*.permissionable_id', [$menu1['id'], $action1['id']])
            ->assertJsonPath('data.*.permissionable_type', [$menu1::class, $action1::class])
            ->assertJsonPath('data.*.permissionable.id', [$menu1['id'], $action1['id']])
            ->assertOk();
    }
}
