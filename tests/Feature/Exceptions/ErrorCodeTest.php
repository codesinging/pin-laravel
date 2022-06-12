<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Exceptions;

use App\Exceptions\ErrorCode;
use Tests\TestCase;

class ErrorCodeTest extends TestCase
{
    public function testInvoke()
    {
        self::assertEquals(ErrorCode::Error->value, ErrorCode::Error());
    }

    public function testLabel()
    {
        self::assertEquals('程序错误', ErrorCode::Error->label());
    }
}
