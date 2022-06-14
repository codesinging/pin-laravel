<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Exceptions;

use App\Exceptions\AdminErrors;
use App\Exceptions\AdminException;
use Tests\TestCase;

class AdminExceptionTest extends TestCase
{
    /**
     * @throws AdminException
     */
    public function testExceptionWithDefaultCode()
    {
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('error');

        throw new AdminException('error');
    }

    /**
     * @throws AdminException
     */
    public function testExceptionWithString()
    {
        $this->expectExceptionCode(100);
        $this->expectExceptionMessage('error');

        throw new AdminException('error', 100);
    }

    /**
     * @throws AdminException
     */
    public function testExceptionWithErrorCode()
    {
        $this->expectExceptionCode(AdminErrors::Error());
        $this->expectExceptionMessage(AdminErrors::Error->label());
        throw new AdminException(AdminErrors::Error);
    }
}
