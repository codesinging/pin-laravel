<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Events;

use App\Models\SettingOption;

class SettingOptionUpdated
{
    public function __construct(SettingOption $settingOption)
    {
        $setting = $settingOption->setting;

        $setting->fill([
            'group_id' => $settingOption['group_id'],
            'key' => $settingOption['key'],
        ]);

        if ($settingOption['initial']){
            $setting['value'] = $settingOption['value'];
        }

        $settingOption->setting()->save($setting);
    }
}
