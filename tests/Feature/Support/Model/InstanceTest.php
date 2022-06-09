<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Model;

use App\Support\Model\BaseModel;
use Tests\TestCase;

class InstanceTest extends TestCase
{
    public function testInstance()
    {
        self::assertInstanceOf(BaseModel::class, BaseModel::instance());
    }
}
