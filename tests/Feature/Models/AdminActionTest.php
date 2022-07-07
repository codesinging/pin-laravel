<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Models;

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController;
use App\Models\AdminAction;
use App\Models\AdminPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionException;
use Tests\TestCase;

class AdminActionTest extends TestCase
{
    use RefreshDatabase;

    public function testPermissionAttribute()
    {
        /** @var AdminAction $action */
        $action = AdminAction::factory()->create();

        self::assertEquals($action['id'], $action['permission']['permissionable_id']);
        self::assertEquals($action::class, $action['permission']['permissionable_type']);
    }

    public function testEvents()
    {
        /** @var AdminAction $action1 */
        $action1 = AdminAction::factory()->create(['public' => false]);

        /** @var AdminAction $action2 */
        $action2 = AdminAction::factory()->create(['public' => true]);

        $this->assertModelExists($action1);
        $this->assertModelExists($action2);

        $this->assertDatabaseHas(AdminPermission::class, [
            'permissionable_id' => $action1['id'],
            'permissionable_type' => $action1::class,
            'guard_name' => $action1->guard_name,
        ]);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $action2['id'],
            'permissionable_type' => $action2::class,
            'guard_name' => $action2->guard_name,
        ]);

        $action1['public'] = true;
        $action2['public'] = false;

        $action1->save();
        $action2->save();

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $action1['id'],
            'permissionable_type' => $action1::class,
            'guard_name' => $action1->guard_name,
        ]);

        $this->assertDatabaseHas(AdminPermission::class, [
            'permissionable_id' => $action2['id'],
            'permissionable_type' => $action2::class,
            'guard_name' => $action2->guard_name,
        ]);

        $action1->delete();
        $action2->delete();

        $this->assertModelMissing($action1);
        $this->assertModelMissing($action2);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $action1['id'],
            'permissionable_type' => $action1::class,
            'guard_name' => $action1->guard_name,
        ]);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $action2['id'],
            'permissionable_type' => $action2::class,
            'guard_name' => $action2->guard_name,
        ]);
    }

    /**
     * @throws ReflectionException
     */
    public function testSyncFrom()
    {
        $route1 = AdminUserController::class . '@index';
        $route2 = AuthController::class . '@user';

        $adminAction1 = AdminAction::syncFrom($route1);
        $adminAction2 = AdminAction::syncFrom($route2);

        self::assertFalse($adminAction1->isPublic());
        self::assertTrue($adminAction2->isPublic());

        $this->assertModelExists($adminAction1);
        $this->assertModelExists($adminAction2);

        $this->assertDatabaseCount(AdminAction::class, 2);

        AdminAction::syncFrom($route1);
        AdminAction::syncFrom($route2);

        $this->assertDatabaseCount(AdminAction::class, 2);

        $this->assertModelExists($adminAction1->permission);
        $this->assertNull($adminAction2->permission);

        self::assertEquals($adminAction1['id'], $adminAction1->permission['permissionable_id']);
    }

    /**
     * @throws ReflectionException
     */
    public function testFindBy()
    {
        $route = AdminUserController::class . '@index';

        $adminAction = AdminAction::syncFrom($route);

        $this->assertModelExists($adminAction);

        $foundAction = AdminAction::findBy($route);

        self::assertEquals($adminAction['id'], $foundAction['id']);
    }
}
