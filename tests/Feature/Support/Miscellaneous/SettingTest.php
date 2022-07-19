<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Miscellaneous;

use App\Models\Setting as SettingModel;
use App\Models\SettingGroup;
use App\Support\Miscellaneous\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function testHas()
    {
        self::assertFalse(Setting::has('s1'));

        SettingModel::factory()->create(['key' => 's1']);

        self::assertTrue(Setting::has('s1'));
        self::assertFalse(Setting::has('s2'));
    }

    public function testGet()
    {
        self::assertNull(Setting::get('s1'));
        self::assertEquals('v1', Setting::get('s1', 'v1'));

        SettingModel::factory()->create(['key' => 's1', 'value' => 'str']);
        SettingModel::factory()->create(['key' => 's2', 'value' => 1]);
        SettingModel::factory()->create(['key' => 's3', 'value' => true]);
        SettingModel::factory()->create(['key' => 's4', 'value' => false]);
        SettingModel::factory()->create(['key' => 's5', 'value' => [1, 2, 3]]);

        self::assertEquals('str', Setting::get('s1'));
        self::assertEquals(1, Setting::get('s2'));
        self::assertEquals(true, Setting::get('s3'));
        self::assertEquals(false, Setting::get('s4'));
        self::assertEquals([1, 2, 3], Setting::get('s5'));
        self::assertEquals(null, Setting::get('s6'));
        self::assertEquals('v7', Setting::get('s7', 'v7'));
    }

    public function testAll()
    {
        SettingModel::factory()->create(['key' => 's1', 'value' => 'str']);
        SettingModel::factory()->create(['key' => 's2', 'value' => 1]);
        SettingModel::factory()->create(['key' => 's3', 'value' => true]);
        SettingModel::factory()->create(['key' => 's4', 'value' => false]);
        SettingModel::factory()->create(['key' => 's5', 'value' => [1, 2, 3]]);

        self::assertEquals([
            's1' => 'str',
            's2' => 1,
            's3' => true,
            's4' => false,
            's5' => [1, 2, 3],
        ], Setting::all()->toArray());

        self::assertEquals([
            's1' => 'str',
            's2' => 1,
            's3' => true,
            's5' => [1, 2, 3],
        ], Setting::all(['s1', 's2', 's3', 's5'])->toArray());

        self::assertEquals([
            's1' => 'str',
            's5' => [1, 2, 3],
        ], Setting::all(['s1', 's5'])->toArray());
    }

    public function testAllByGroup()
    {
        $group1 = SettingGroup::factory()->create(['key' => 'g1']);
        $group2 = SettingGroup::factory()->create(['key' => 'g2']);
        $group3 = SettingGroup::factory()->create(['key' => 'g3']);

        SettingModel::factory()->create(['key' => 's1', 'group_id' => $group1['id'], 'value' => 'str']);
        SettingModel::factory()->create(['key' => 's2', 'group_id' => $group1['id'], 'value' => 1]);
        SettingModel::factory()->create(['key' => 's3', 'group_id' => $group2['id'], 'value' => true]);

        self::assertEquals([
            's1' => 'str',
            's2' => 1,
        ], Setting::allByGroup('g1')->toArray());

        self::assertEquals([
            's3' => true,
        ], Setting::allByGroup('g2')->toArray());

        self::assertEquals([], Setting::allByGroup('g3')->toArray());
    }
}
