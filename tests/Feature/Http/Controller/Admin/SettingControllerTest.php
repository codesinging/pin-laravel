<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testUpdate()
    {
        $setting = Setting::factory()->create(['value' => 1]);

        self::assertEquals(1, $setting['value']);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/settings/' . $setting['id'], ['value' => 2])
            ->assertJsonPath('data.value', 2)
            ->assertOk();

        $setting->refresh();

        self::assertEquals(2, $setting['value']);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/settings/' . $setting['id'], ['value' => 'a'])
            ->assertJsonPath('data.value', 'a')
            ->assertOk();

        $setting->refresh();

        self::assertEquals('a', $setting['value']);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/settings/' . $setting['id'], ['value' => ['a']])
            ->assertJsonPath('data.value', ['a'])
            ->assertOk();

        $setting->refresh();

        self::assertEquals(['a'], $setting['value']);
    }
}
