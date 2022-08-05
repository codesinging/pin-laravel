<?php

namespace Database\Seeders;

use App\Enums\SettingTypes;
use App\Models\SettingGroup;
use App\Models\SettingOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            SettingOption::creates($data);
        }
    }

    public function data(): array
    {
        return [
            ['group_id' => $this->groupId('site'), 'name' => '网站名称', 'key' => 'site_name', 'type' => SettingTypes::Input->name, 'value' => '品凡网络'],
            ['group_id' => $this->groupId('site'), 'name' => '网站标题', 'key' => 'site_title', 'type' => SettingTypes::Input->name, 'value' => '专注网络软件开发'],
            ['group_id' => $this->groupId('site'), 'name' => '网站关键词', 'key' => 'site_keywords', 'type' => SettingTypes::Input->name, 'value' => '软件开发,微信小程序开发,微信公众号开发'],
            ['group_id' => $this->groupId('site'), 'name' => '网站描述', 'key' => 'site_description', 'type' => SettingTypes::Textarea->name, 'value' => '品凡网络是一家专注于网络软件开发的创新科技企业'],
            ['group_id' => $this->groupId('site'), 'name' => 'ICP备案号', 'key' => 'site_icp_beian', 'type' => SettingTypes::Input->name, 'value' => ''],
            ['group_id' => $this->groupId('admin'), 'name' => '是否启用登录验证码', 'key' => 'admin_captcha_enabled', 'type' => SettingTypes::Switch->name, 'value' => false],
        ];
    }

    public function groupId(string $key)
    {
        return SettingGroup::wheres('key', $key)->value('id');
    }
}
