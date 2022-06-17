<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Models;

use App\Http\Controllers\Admin\AdminUserController;
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

    public function testWithPermission()
    {
        AdminAction::factory()->create();

        $action = AdminAction::firsts();

        self::assertArrayHasKey('permission', $action->toArray());

        self::assertEquals($action['id'], $action['permission']['permissionable_id']);
        self::assertEquals($action::class, $action['permission']['permissionable_type']);
    }

    public function testCreatedAndDeletedEvent()
    {
        /** @var AdminAction $action */
        $action = AdminAction::factory()->create();

        $this->assertModelExists($action);

        $this->assertDatabaseHas(AdminPermission::class, [
            'permissionable_id' => $action['id'],
            'permissionable_type' => $action::class,
            'guard_name' => $action->guard_name,
        ]);

        $action->delete();

        $this->assertModelMissing($action);

        $this->assertDatabaseMissing(AdminPermission::class, [
            'permissionable_id' => $action['id'],
            'permissionable_type' => $action::class,
            'guard_name' => $action->guard_name,
        ]);
    }

    /**
     * @throws ReflectionException
     */
    public function testSyncFrom()
    {
        $route = AdminUserController::class . '@index';

        $adminAction = AdminAction::syncFrom($route);

        $this->assertModelExists($adminAction);

        $this->assertDatabaseCount(AdminAction::class, 1);

        AdminAction::syncFrom($route);

        $this->assertDatabaseCount(AdminAction::class, 1);

        $this->assertModelExists($adminAction->permission);

        self::assertEquals($adminAction['id'], $adminAction->permission['permissionable_id']);
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
