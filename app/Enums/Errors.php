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
 * 通用错误
 *
 * @method static int Error()
 * @method static int Forbidden()
 *
 * @method string description()
 */
#[Meta(Description::class)]
enum Errors: int
{
    use InvokableCases;
    use Metadata;

    #[Description('响应错误')]
    case Error = 1;

    #[Description('禁止操作')]
    case Forbidden = 900001;
}
