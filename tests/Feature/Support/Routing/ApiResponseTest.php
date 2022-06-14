<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Routing;

use App\Exceptions\Errors;
use App\Support\Routing\ApiResponse;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    public function testSuccess()
    {
        self::assertInstanceOf(JsonResponse::class, ApiResponse::success());

        self::assertEquals(200, ApiResponse::success()->status());
        self::assertNull(ApiResponse::success()->getData(true)['message']);
        self::assertEquals('message', ApiResponse::success('message')->getData(true)['message']);
        self::assertEquals(0, ApiResponse::success()->getData(true)['code']);
        self::assertEquals(['id' => 1], ApiResponse::success(['id' => 1])->getData(true)['data']);
        self::assertEquals(['id' => 1], ApiResponse::success('message', ['id' => 1])->getData(true)['data']);
    }

    public function testError()
    {
        self::assertInstanceOf(JsonResponse::class, ApiResponse::error());

        self::assertEquals(200, ApiResponse::error()->status());
        self::assertNull(ApiResponse::error()->getData(true)['message']);
        self::assertEquals('error', ApiResponse::error('error')->getData(true)['message']);
        self::assertEquals(1, ApiResponse::error('error')->getData(true)['code']);
        self::assertEquals(900100, ApiResponse::error('error', 900100)->getData(true)['code']);
        self::assertEquals(Errors::Error->label(), ApiResponse::error(Errors::Error)->getData(true)['message']);
        self::assertEquals(Errors::Error(), ApiResponse::error(Errors::Error)->getData(true)['code']);
    }
}
