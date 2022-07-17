<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Events;

use App\Models\SettingOption;

class SettingOptionDeleted
{
    public function __construct(SettingOption $settingOption)
    {
        $settingOption->setting()->delete();
    }
}
