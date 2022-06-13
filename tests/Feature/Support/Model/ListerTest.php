<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Model;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListerTest extends TestCase
{
    use RefreshDatabase;

    public function testListerWithPage()
    {
        $this->seed(UserSeeder::class);

        request()->merge(['page' => 1]);

        $lister = User::instance()->lister();

        self::assertIsArray($lister);

        self::assertArrayHasKey('page', $lister);
        self::assertArrayHasKey('size', $lister);
        self::assertArrayHasKey('data', $lister);
        self::assertArrayHasKey('total', $lister);
        self::assertArrayHasKey('more', $lister);

        self::assertEquals(1, $lister['page']);
    }

    public function testListerWithoutPage()
    {
        $this->seed(UserSeeder::class);

        $count = User::instance()->count();

        $lister = User::instance()->lister();

        self::assertIsArray($lister->toArray());
        self::assertCount($count, $lister);
    }
}
