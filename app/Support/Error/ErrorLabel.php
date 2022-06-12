<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Error;

interface ErrorLabel
{
    /**
     * @return string
     */
    public function label(): string;
}
