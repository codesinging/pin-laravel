<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Exceptions;

use Throwable;

class Exception extends \Exception
{
    public function __construct(string|Errors $message = "", int $code = 1, ?Throwable $previous = null)
    {
        if ($message instanceof Errors) {
            $code = $message->value;
            $message = $message->label();
        }
        parent::__construct($message, $code, $previous);
    }
}