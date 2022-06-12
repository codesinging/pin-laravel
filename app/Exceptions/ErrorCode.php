<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Exceptions;

use App\Support\Error\ErrorLabel;
use ArchTech\Enums\InvokableCases;

/**
 * @method static int Error()
 */
enum ErrorCode: int implements ErrorLabel
{
    use InvokableCases;

    case Error = 1;

    public function label(): string
    {
        return match ($this) {
            self::Error => '响应错误',
        };
    }
}
