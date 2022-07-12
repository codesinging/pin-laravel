<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Models\SettingGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class SettingGroupControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testIndex()
    {
        SettingGroup::factory()->count(3)->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/setting_groups')
            ->assertJsonCount(3, 'data')
            ->assertOk();
    }

    public function testStore()
    {
        $this->assertDatabaseCount(SettingGroup::class, 0);

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/setting_groups', ['name' => 'test'])
            ->assertJsonPath('data.name', 'test')
            ->assertOk();

        $this->assertDatabaseCount(SettingGroup::class, 1);
    }

    public function testUpdate()
    {
        $group = SettingGroup::factory()->create(['name' => 'test1']);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/setting_groups/' . $group['id'], ['name' => 'test2'])
            ->assertJsonPath('data.name', 'test2')
            ->assertOk();

        $group->refresh();

        self::assertEquals('test2', $group['name']);
    }

    public function testShow()
    {
        $group = SettingGroup::factory()->create(['name' => 'test1']);

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/setting_groups/' . $group['id'])
            ->assertJsonPath('data.name', 'test1')
            ->assertOk();
    }

    public function testDestroy()
    {
        $group = SettingGroup::factory()->create(['name' => 'test1']);

        $this->assertModelExists($group);

        $this->actingAsSuperAdminUser()
            ->deleteJson('api/admin/setting_groups/' . $group['id'])
            ->assertOk();

        $this->assertModelMissing($group);
    }
}
