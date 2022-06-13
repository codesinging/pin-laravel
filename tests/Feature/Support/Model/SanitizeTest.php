<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Model;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanitizeTest extends TestCase
{
    use RefreshDatabase;

    public function testParameterIsArray()
    {
        self::assertEquals(['name' => 'Name'], User::instance()->sanitize(['name' => 'Name', 'not_exist' => 'NotExist']));
    }

    public function testParameterIsRequest()
    {
        request()->merge(['name' => 'Name', 'not_exist' => 'NotExist']);

        self::assertEquals(['name' => 'Name'], User::instance()->sanitize(request()));
    }
}
