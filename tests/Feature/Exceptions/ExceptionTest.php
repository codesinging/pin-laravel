<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Exceptions;

use App\Exceptions\ErrorCode;
use App\Exceptions\Exception;
use Tests\TestCase;

class ExceptionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testExceptionWithDefaultCode()
    {
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('error');

        throw new Exception('error');
    }

    /**
     * @throws Exception
     */
    public function testExceptionWithString()
    {
        $this->expectExceptionCode(100);
        $this->expectExceptionMessage('error');

        throw new Exception('error', 100);
    }

    /**
     * @throws Exception
     */
    public function testExceptionWithErrorCode()
    {
        $this->expectExceptionCode(ErrorCode::Error());
        $this->expectExceptionMessage(ErrorCode::Error->label());
        throw new Exception(ErrorCode::Error);
    }
}
