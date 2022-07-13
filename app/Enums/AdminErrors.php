<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Enums;

use App\Support\Miscellaneous\EnumLabel;
use ArchTech\Enums\InvokableCases;

/**
 * 后台管理错误
 *
 * @method static int Error()
 */
enum AdminErrors: int implements EnumLabel
{
    use InvokableCases;

    case Error = 1;

    case AuthUserNotFound = 900100;
    case AuthNotMatched = 900101;
    case AuthInvalidStatus = 900102;
    case AuthLoginErrorLimit = 900103;

    case NoPermission = 900200;

    public function label(): string
    {
        return match ($this) {
            self::Error => '响应错误',
            self::AuthUserNotFound => '登录用户不存在',
            self::AuthNotMatched => '账号和密码不匹配',
            self::AuthInvalidStatus => '账号状态异常',
            self::AuthLoginErrorLimit => '登录错误次数达到限制',

            self::NoPermission => '无访问权限',
        };
    }
}
