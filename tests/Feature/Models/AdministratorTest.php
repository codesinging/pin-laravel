<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Models;

use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdministratorTest extends TestCase
{
    use RefreshDatabase;

    public function testPasswordAttribute()
    {
        $admin = Administrator::creates([
            'username' => 'admin',
            'name' => 'Admin',
            'password' => 'admin.123',
        ]);

        self::assertTrue(Hash::check('admin.123', $admin['password']));
        self::assertFalse(Hash::check('admin.111', $admin['password']));

        $admin->fill(['password' => 'admin.111'])->save();

        self::assertTrue(Hash::check('admin.111', $admin['password']));
        self::assertFalse(Hash::check('admin.123', $admin['password']));

        $admin->fill(['name' => 'admin_name'])->save();

        self::assertTrue(Hash::check('admin.111', $admin['password']));
        self::assertEquals('admin_name', $admin['name']);

        $admin->update(['password' => 'admin.222']);

        self::assertTrue(Hash::check('admin.222', $admin['password']));

        $admin->password = 'admin.333';
        $admin->save();

        self::assertTrue(Hash::check('admin.333', $admin['password']));

        $admin['password'] = 'admin.444';
        $admin->save();

        self::assertTrue(Hash::check('admin.444', $admin['password']));
    }
}
