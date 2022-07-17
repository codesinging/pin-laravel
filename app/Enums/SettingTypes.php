<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Enums;

use App\Support\Miscellaneous\EnumOf;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Options;

enum SettingTypes: string
{
    use InvokableCases;
    use Options;
    use EnumOf;

    case Input = '输入框';
    case Textarea = '多行文本输入框';
    case InputNumber = '数字输入框';
    case CheckboxGroup = '复选框组';
    case RadioGroup = '单选框组';
    case SingleSelect = '单选选择框';
    case MultipleSelect = '多选选择框';
    case ColorPicker = '颜色选择器';
    case DatePicker = '日期选择器';
    case DateTimePicker = '日期时间选择器';
    case TimePicker = '时间选择器';
    case TimeSelect = '时间选择框';
    case Switch = '开关';
}
