<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Exceptions;

use App\Enums\Errors;
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
        $this->expectExceptionCode(Errors::Error());
        $this->expectExceptionMessage(Errors::Error->description());
        throw new Exception(Errors::Error);
    }
}
