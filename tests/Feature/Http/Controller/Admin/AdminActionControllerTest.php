<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Http\Controllers\Admin\AdminUserController;
use App\Models\AdminAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class AdminActionControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testIndex()
    {
        AdminAction::factory()->count(5)->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_actions')
            ->assertJsonCount(5, 'data')
            ->assertOk();
    }

    public function testShow()
    {
        $action = AdminAction::factory()->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_actions/' . $action['id'])
            ->assertJsonPath('data.id', $action['id'])
            ->assertOk();
    }

    public function testDestroy()
    {
        $action = AdminAction::factory()->create();

        $this->actingAsSuperAdminUser()
            ->deleteJson('api/admin/admin_actions/' . $action['id'])
            ->assertJsonPath('data.id', $action['id'])
            ->assertOk();

        $this->assertModelMissing($action);
    }

    public function testSync()
    {
        $this->assertDatabaseCount(AdminAction::class, 0);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_actions/sync')
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->assertDatabaseHas(AdminAction::class, [
            'controller' => AdminUserController::class,
            'action' => 'index',
        ]);
    }
}
