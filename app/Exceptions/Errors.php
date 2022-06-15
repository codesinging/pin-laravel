<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Exceptions;

use ArchTech\Enums\InvokableCases;

/**
 * 通用错误
 *
 * @method static int Error()
 * @method static int Forbidden()
 */
enum Errors: int implements ErrorLabel
{
    use InvokableCases;

    case Error = 1;

    case Forbidden = 900001;

    public function label(): string
    {
        return match ($this) {
            self::Error => '响应错误',
            self::Forbidden => '禁止操作',
        };
    }
}
