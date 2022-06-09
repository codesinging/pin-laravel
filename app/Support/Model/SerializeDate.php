<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use DateTimeInterface;

trait SerializeDate
{
    /**
     * 设置序列化数据时的日期格式.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
}
