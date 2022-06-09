<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Helpers;

class Snowflake
{
    /**
     * @return \Kra8\Snowflake\Snowflake
     */
    public static function instance(): \Kra8\Snowflake\Snowflake
    {
        return app(\Kra8\Snowflake\Snowflake::class);
    }

    /**
     * 生成 64 位唯一数字 ID
     * @return int
     */
    public static function id(): int
    {
        return self::instance()->id();
    }

    /**
     * 生成 53 位唯一数字 ID，适用于 JS
     * @return int
     */
    public static function short(): int
    {
        return self::instance()->short();
    }
}
