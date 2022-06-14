<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Exceptions;

use App\Exceptions\AdminErrors;
use Tests\TestCase;

class AdminErrorsTest extends TestCase
{
    public function testInvoke()
    {
        self::assertEquals(AdminErrors::Error->value, AdminErrors::Error());
    }

    public function testLabel()
    {
        self::assertEquals('响应错误', AdminErrors::Error->label());
    }
}
