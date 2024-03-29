<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Exceptions;

use App\Enums\AdminErrors;
use Throwable;

/**
 * 后台管理异常类
 */
class AdminException extends \Exception
{
    public function __construct(string|AdminErrors $message = "", int $code = 1, ?Throwable $previous = null)
    {
        if ($message instanceof AdminErrors) {
            $code = $message->value;
            $message = $message->description();
        }
        parent::__construct($message, $code, $previous);
    }
}
