<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Events;

use App\Models\SettingOption;

class SettingOptionCreated
{
    public function __construct(SettingOption $settingOption)
    {
        $settingOption->setting()->create([
            'group_id' => $settingOption['group_id'],
            'key' => $settingOption['key'],
            'value' => $settingOption['value'],
        ]);
    }
}
