<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Exceptions;

interface ErrorLabel
{
    /**
     * @return string
     */
    public function label(): string;
}
