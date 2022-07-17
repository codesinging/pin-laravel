<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Models;

use App\Enums\SettingTypes;
use App\Models\Setting;
use App\Models\SettingOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingOptionTest extends TestCase
{
    use RefreshDatabase;

    public function testEvents()
    {
        /** @var SettingOption $option */
        $option = SettingOption::factory()->create([
            'key' => 'key',
            'group_id' => 111,
            'value' => 'value'
        ]);

        $this->assertModelExists($option);
        $this->assertModelExists($option->setting);

        self::assertEquals(111, $option->setting['group_id']);
        self::assertEquals('key', $option->setting['key']);
        self::assertEquals('value', $option->setting['value']);

        $option['key'] = 'newKey';
        $option['group_id'] = 222;
        $option['value'] = 'new value';

        $option->save();
        $option->refresh();

        self::assertEquals(222, $option->setting['group_id']);
        self::assertEquals('newKey', $option->setting['key']);
        self::assertEquals('new value', $option->setting['value']);

        $option['key'] = 'newKey2';
        $option['group_id'] = 333;
        $option['value'] = 'new new value';
        $option['initial'] = false;

        $option->save();
        $option->refresh();

        self::assertEquals(333, $option->setting['group_id']);
        self::assertEquals('newKey2', $option->setting['key']);
        self::assertEquals('new value', $option->setting['value']);

        $option->delete();

        $this->assertModelMissing($option);
        $this->assertModelMissing($option->setting);
        $this->assertDatabaseCount(Setting::class, 0);
    }

    public function testTypeLabel()
    {
        $option = SettingOption::factory()->create(['type' => 'Input']);

        self::assertEquals(SettingTypes::Input->value, $option['type_label']);
    }
}
