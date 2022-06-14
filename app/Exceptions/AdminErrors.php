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
enum AdminErrors: int implements ErrorLabel
{
    use InvokableCases;

    case Error = 1;

    case AuthUserNotFound = 900100;

    public function label(): string
    {
        return match ($this){
            self::Error => '响应错误',
            self::AuthUserNotFound => '登录用户不存在',
        };
    }
}
