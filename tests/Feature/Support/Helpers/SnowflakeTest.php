<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Helpers;

use App\Support\Helpers\Snowflake;
use Tests\TestCase;

class SnowflakeTest extends TestCase
{
    public function testId()
    {
        self::assertIsInt(Snowflake::id());
        self::assertGreaterThanOrEqual(18, strlen(Snowflake::id()));
    }

    public function testShort()
    {
        self::assertIsInt(Snowflake::short());
        self::assertLessThanOrEqual(15, strlen(Snowflake::short()));
    }
}
