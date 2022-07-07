<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Models;

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController;
use App\Models\AdminRoute;
use App\Models\AdminPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionException;
use Tests\TestCase;

class AdminRouteTest extends TestCase
{
    use RefreshDatabase;

    public function testPermissionAttribute()
    {
        /** @var AdminRoute $route */
        $route = AdminRoute::factory()->create();

        self::assertEquals($route['id'], $route['permission']['permissionable_id']);
        self::assertEquals($route::class, $route['permission']['permissionable_type']);
    }

    public function testEvents()
    {
        /** @var AdminRoute $route1 */
        $route1 = AdminRoute::factory()->create(['public' => false]);

        /** @var AdminRoute $route2 */
        $route2 = AdminRoute::factory()->create(['public' => true]);

        $this->assertModelExists($route1);
        $this->assertModelExists($route2);

        $this->assertDatabaseHas(AdminPermission::class, [
            'permissionable_id' => $route1['id'],
            'permissionable_type' => $route1::class,
            'guard_name' => $route1->guard_name,
        ]);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $route2['id'],
            'permissionable_type' => $route2::class,
            'guard_name' => $route2->guard_name,
        ]);

        $route1['public'] = true;
        $route2['public'] = false;

        $route1->save();
        $route2->save();

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $route1['id'],
            'permissionable_type' => $route1::class,
            'guard_name' => $route1->guard_name,
        ]);

        $this->assertDatabaseHas(AdminPermission::class, [
            'permissionable_id' => $route2['id'],
            'permissionable_type' => $route2::class,
            'guard_name' => $route2->guard_name,
        ]);

        $route1->delete();
        $route2->delete();

        $this->assertModelMissing($route1);
        $this->assertModelMissing($route2);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $route1['id'],
            'permissionable_type' => $route1::class,
            'guard_name' => $route1->guard_name,
        ]);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $route2['id'],
            'permissionable_type' => $route2::class,
            'guard_name' => $route2->guard_name,
        ]);
    }

    /**
     * @throws ReflectionException
     */
    public function testSyncFrom()
    {
        $route1 = AdminUserController::class . '@index';
        $route2 = AuthController::class . '@user';

        $adminRoute1 = AdminRoute::syncFrom($route1);
        $adminRoute2 = AdminRoute::syncFrom($route2);

        self::assertFalse($adminRoute1->isPublic());
        self::assertTrue($adminRoute2->isPublic());

        $this->assertModelExists($adminRoute1);
        $this->assertModelExists($adminRoute2);

        $this->assertDatabaseCount(AdminRoute::class, 2);

        AdminRoute::syncFrom($route1);
        AdminRoute::syncFrom($route2);

        $this->assertDatabaseCount(AdminRoute::class, 2);

        $this->assertModelExists($adminRoute1->permission);
        $this->assertNull($adminRoute2->permission);

        self::assertEquals($adminRoute1['id'], $adminRoute1->permission['permissionable_id']);
    }

    /**
     * @throws ReflectionException
     */
    public function testFindBy()
    {
        $route = AdminUserController::class . '@index';

        $adminRoute = AdminRoute::syncFrom($route);

        $this->assertModelExists($adminRoute);

        $foundRoute = AdminRoute::findBy($route);

        self::assertEquals($adminRoute['id'], $foundRoute['id']);
    }
}
