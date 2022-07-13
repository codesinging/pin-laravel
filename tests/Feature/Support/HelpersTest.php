<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support;

use App\Enums\Errors;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function testSuccess()
    {
        self::assertInstanceOf(JsonResponse::class, success());

        self::assertEquals(200, success()->status());
        self::assertNull(success()->getData(true)['message']);
        self::assertEquals('message', success('message')->getData(true)['message']);
        self::assertEquals(0, success()->getData(true)['code']);
        self::assertEquals(['id' => 1], success(['id' => 1])->getData(true)['data']);
        self::assertEquals(['id' => 1], success('message', ['id' => 1])->getData(true)['data']);
    }

    public function testError()
    {
        self::assertInstanceOf(JsonResponse::class, error());

        self::assertEquals(200, error()->status());
        self::assertNull(error()->getData(true)['message']);
        self::assertEquals('error', error('error')->getData(true)['message']);
        self::assertEquals(1, error('error')->getData(true)['code']);
        self::assertEquals(900100, error('error', 900100)->getData(true)['code']);
        self::assertEquals(Errors::Error->label(), error(Errors::Error)->getData(true)['message']);
        self::assertEquals(Errors::Error(), error(Errors::Error)->getData(true)['code']);
    }
}
