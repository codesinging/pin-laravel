<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Http\Controllers\Admin\AdminUserController;
use App\Models\AdminRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class AdminRouteControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testIndex()
    {
        AdminRoute::factory()->count(5)->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_routes')
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure(['data' => ['*' => ['permission']]])
            ->assertOk();
    }

    public function testShow()
    {
        $route = AdminRoute::factory()->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_routes/' . $route['id'])
            ->assertJsonPath('data.id', $route['id'])
            ->assertJsonPath('data.permission.permissionable_id', $route['id'])
            ->assertOk();
    }

    public function testDestroy()
    {
        $route = AdminRoute::factory()->create();

        $this->actingAsSuperAdminUser()
            ->deleteJson('api/admin/admin_routes/' . $route['id'])
            ->assertJsonPath('data.id', $route['id'])
            ->assertOk();

        $this->assertModelMissing($route);
    }

    public function testSync()
    {
        $this->assertDatabaseCount(AdminRoute::class, 0);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_routes/sync')
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->assertDatabaseHas(AdminRoute::class, [
            'controller' => AdminUserController::class,
            'action' => 'index',
        ]);

        $count = AdminRoute::instance()->count();

        AdminRoute::factory()->count(3)->create();

        $this->assertDatabaseCount(AdminRoute::class, $count + 3);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_routes/sync')
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->assertDatabaseCount(AdminRoute::class, $count);
    }
}
