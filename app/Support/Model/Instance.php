<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

trait Instance
{
    /**
     * 返回一个模型实例
     *
     * @param array $attributes
     *
     * @return static
     */
    public static function instance(array $attributes = []): static
    {
        return new static($attributes);
    }
}
