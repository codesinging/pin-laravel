<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Exceptions;

use App\Enums\Errors;
use Tests\TestCase;

class ErrorsTest extends TestCase
{
    public function testInvoke()
    {
        self::assertEquals(Errors::Error->value, Errors::Error());
    }

    public function testLabel()
    {
        self::assertEquals('响应错误', Errors::Error->label());
    }
}
