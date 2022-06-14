<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Exceptions;

use ArchTech\Enums\InvokableCases;

/**
 * @method static int Error()
 */
enum Errors: int implements ErrorLabel
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
