<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Enums\SettingTypes;
use App\Models\Setting;
use App\Models\SettingOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class SettingOptionControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testIndex()
    {
        SettingOption::factory()->count(5)->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/setting_options')
            ->assertJsonCount(5, 'data')
            ->assertOk();
    }

    public function testStore()
    {
        $data = [
            'group_id' => 1,
            'name' => 'Name',
            'key' => 'name',
            'type' => 'Input'
        ];

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/setting_options', $data)
            ->assertJsonPath('data.name', $data['name'])
            ->assertOk();

        $this->assertDatabaseHas(SettingOption::class, $data);
        $this->assertDatabaseHas(Setting::class, ['key' => $data['key']]);
    }

    public function testUpdate()
    {
        /** @var SettingOption $option */
        $option = SettingOption::factory()->create();

        $data = [
            'group_id' => 1,
            'name' => 'Name',
            'key' => 'name',
            'type' => 'Input'
        ];

        $this->assertModelExists($option);
        $this->assertModelExists($option->setting);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/setting_options/' . $option['id'], $data)
            ->assertJsonPath('data.name', $data['name'])
            ->assertJsonPath('data.key', $data['key'])
            ->assertOk();

        $option->refresh();

        self::assertEquals($data['key'], $option->setting['key']);
    }

    public function testShow()
    {
        $option = SettingOption::factory()->create();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/setting_options/' . $option['id'])
            ->assertJsonPath('data.name', $option['name'])
            ->assertOk();
    }

    public function testDestroy()
    {
        $option = SettingOption::factory()->create();

        $this->assertModelExists($option);

        $this->actingAsSuperAdminUser()
            ->deleteJson('api/admin/setting_options/' . $option['id'])
            ->assertOk();

        $this->assertModelMissing($option);
    }

    public function testTypes()
    {
        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/setting_options/types')
            ->assertJsonPath('data.Input', SettingTypes::Input->value)
            ->assertOk();
    }
}
