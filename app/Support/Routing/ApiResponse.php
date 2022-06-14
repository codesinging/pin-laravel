<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Routing;

use App\Exceptions\AdminErrors;
use App\Exceptions\Errors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class ApiResponse
{
    /**
     * 返回成功的 json 响应
     *
     * @param string|array|Model|Collection|null $message
     * @param array|Model|Collection|null $data
     *
     * @return JsonResponse
     */
    public static function success(string|array|Model|Collection $message = null, array|Model|Collection $data = null): JsonResponse
    {
        is_string($message) or list($data, $message) = [$message, $data];
        $code = 0;
        return response()->json(compact('code', 'message', 'data'));
    }

    /**
     * 返回错误的 json 响应
     *
     * @param Errors|AdminErrors|string|null $message
     * @param int $code
     * @param mixed|null $data
     *
     * @return JsonResponse
     */
    public static function error(Errors|AdminErrors|string $message = null, int $code = 1, mixed $data = null): JsonResponse
    {
        if ($message instanceof Errors || $message instanceof AdminErrors) {
            $code = $message->value;
            $message = $message->label();
        }
        return response()->json(compact('message', 'code', 'data'));
    }
}
