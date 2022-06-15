<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Exceptions\Errors;
use App\Models\Administrator;
use App\Models\AdminRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\ActingAsAdministrator;
use Tests\TestCase;

class AdministratorControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdministrator;

    public function testIndex()
    {
        /** @var Administrator $admin1 */
        $admin1 = Administrator::factory()->create(['username' => 'admin1']);

        /** @var Administrator $admin2 */
        $admin2 = Administrator::factory()->create(['username' => 'admin2']);

        /** @var Administrator $admin3 */
        $admin3 = Administrator::factory()->create(['username' => 'admin3']);

        /** @var Administrator $admin4 */
        $admin4 = Administrator::factory()->create(['username' => 'admin4']);

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
            ->getJson('api/admin/administrators')
            ->assertJsonCount(4, 'data')
            ->assertOk();

        $this->actingAs($admin1)
            ->getJson('api/admin/administrators?role=' . $role1['id'])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.*.id', [$admin1['id'], $admin3['id']])
            ->assertOk();

        $this->actingAs($admin1)
            ->getJson('api/admin/administrators?role=' . $role4['id'])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.*.id', [$admin4['id']])
            ->assertOk();

        $this->actingAs($admin1)
            ->getJson('api/admin/administrators?role=' . $role3['id'])
            ->assertJsonCount(0, 'data')
            ->assertOk();
    }

    public function testStore()
    {
        $this->actingAsSuperAdministrator()
            ->postJson('api/admin/administrators')
            ->assertJsonStructure(['errors' => ['username', 'name']])
            ->assertStatus(422);

        $this->actingAsSuperAdministrator()
            ->postJson('api/admin/administrators', ['username' => 'username', 'name' => 'name'])
            ->assertJsonStructure(['errors' => ['password']])
            ->assertStatus(422);

        Administrator::factory()->create(['username' => 'username', 'name' => 'name']);

        $this->actingAsSuperAdministrator()
            ->postJson('api/admin/administrators', ['username' => 'username', 'name' => 'name', 'password' => 'admin'])
            ->assertJsonStructure(['errors' => ['username', 'name']])
            ->assertStatus(422);

        $this->actingAsSuperAdministrator()
            ->postJson('api/admin/administrators', ['username' => 'username1', 'name' => 'name1', 'password' => 'admin'])
            ->assertJsonPath('data.username', 'username1')
            ->assertOk();

        $this->assertDatabaseHas(Administrator::class, ['username' => 'username1']);
    }

    public function testUpdate()
    {
        $admin1 = Administrator::factory()->create(['username' => 'admin1']);
        $admin2 = Administrator::factory()->create(['username' => 'admin2', 'super' => true]);

        $this->actingAsSuperAdministrator()
            ->putJson('api/admin/administrators/' . $admin1['id'])
            ->assertJsonStructure(['errors' => ['username', 'name']])
            ->assertStatus(422);

        $this->actingAsSuperAdministrator()
            ->putJson('api/admin/administrators/' . $admin1['id'], ['username' => 'username'])
            ->assertJsonStructure(['errors' => ['name']])
            ->assertStatus(422);

        $this->actingAsSuperAdministrator()
            ->putJson('api/admin/administrators/' . $admin1['id'], ['username' => 'admin2', 'name' => 'name'])
            ->assertJsonStructure(['errors' => ['username']])
            ->assertJsonValidationErrors(['username' => ['登录账号 已经存在。']])
            ->assertStatus(422);

        $this->actingAsSuperAdministrator()
            ->putJson('api/admin/administrators/' . $admin2['id'], ['username' => 'username', 'name' => 'name'])
            ->assertJsonPath('code', Errors::Forbidden())
            ->assertOk();

        $this->actingAsSuperAdministrator()
            ->putJson('api/admin/administrators/' . $admin1['id'], ['username' => 'username', 'name' => 'name'])
            ->assertJsonPath('data.username', 'username')
            ->assertJsonPath('data.name', 'name')
            ->assertOk();

        $this->actingAsSuperAdministrator()
            ->putJson('api/admin/administrators/' . $admin1['id'], ['username' => 'username', 'name' => 'name', 'password' => 'pass'])
            ->assertOk();

        $admin1->refresh();

        self::assertTrue(Hash::check('pass', $admin1['password']));
    }

    public function testShow()
    {
        $admin = Administrator::factory()->create(['username' => 'username']);

        $this->actingAsSuperAdministrator()
            ->getJson('api/admin/administrators/' . $admin['id'])
            ->assertJsonPath('data.username', 'username')
            ->assertOk();
    }

    public function testDestroy()
    {
        $admin1 = Administrator::factory()->create(['username' => 'admin1']);
        $admin2 = Administrator::factory()->create(['username' => 'admin2', 'super' => true]);

        $this->actingAsSuperAdministrator()
            ->deleteJson('api/admin/administrators/'. $admin2['id'])
            ->assertJsonPath('code', Errors::Forbidden())
            ->assertOk();

        $this->actingAsSuperAdministrator()
            ->deleteJson('api/admin/administrators/'. $admin1['id'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->assertModelMissing($admin1);
        $this->assertModelExists($admin2);
    }
}
