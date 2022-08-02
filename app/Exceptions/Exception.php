<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Exceptions;

use App\Enums\Errors;
use Throwable;

/**
 * 通用异常类
 */
class Exception extends \Exception
{
    public function __construct(string|Errors $message = "", int $code = 1, ?Throwable $previous = null)
    {
        if ($message instanceof Errors) {
            $code = $message->value;
            $message = $message->description();
        }
        parent::__construct($message, $code, $previous);
    }
}
