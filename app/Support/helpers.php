<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

use App\Enums\AdminErrors;
use App\Enums\Errors;
use App\Support\Routing\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

if (!function_exists('success')) {
    /**
     * 返回成功的 json 响应
     *
     * @param string|array|Model|Collection|null $message
     * @param array|Model|Collection|null $data
     *
     * @return JsonResponse
     */
    function success(string|array|Model|Collection $message = null, array|Model|Collection $data = null): JsonResponse
    {
        return ApiResponse::success($message, $data);
    }
}

if (!function_exists('error')) {
    /**
     * 返回错误的 json 响应
     *
     * @param Errors|AdminErrors|string|null $message
     * @param int $code
     * @param array|Model|Collection|null $data
     *
     * @return JsonResponse
     */
    function error(Errors|AdminErrors|string $message = null, int $code = 1, mixed $data = null): JsonResponse
    {
        return ApiResponse::error($message, $code, $data);
    }
}
