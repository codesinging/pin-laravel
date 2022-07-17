<?php

namespace Database\Seeders;

use App\Models\SettingGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SettingGroup::creates(['name' => '网站设置', 'key' => 'site', 'description' => '网站基础信息设置', 'sort' => 90]);
        SettingGroup::creates(['name' => '后台设置', 'key' => 'admin', 'description' => '网站后台功能设置', 'sort' => 80]);
    }
}
