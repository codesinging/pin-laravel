<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Enums;

use App\Enums\MetaProperties\Description;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;

/**
 * 后台管理错误
 *
 * @method string description()
 *
 * @method static int Error()
 * @method static int AuthUserNotFound()
 * @method static int AuthNotMatched()
 * @method static int AuthInvalidStatus()
 * @method static int AuthLoginErrorLimit()
 * @method static int NoPermission()
 *
 */
#[Meta(Description::class)]
enum AdminErrors: int
{
    use InvokableCases;
    use Metadata;

    #[Description('响应错误')]
    case Error = 1;

    #[Description('登录用户不存在')]
    case AuthUserNotFound = 900100;

    #[Description('账号和密码不匹配')]
    case AuthNotMatched = 900101;

    #[Description('账号状态异常')]
    case AuthInvalidStatus = 900102;

    #[Description('登录错误次数达到限制')]
    case AuthLoginErrorLimit = 900103;

    #[Description('无访问权限')]
    case NoPermission = 900200;
}
