<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Models;

use App\Models\AdminUser;
use App\Models\AdminRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function testPasswordAttribute()
    {
        $admin = AdminUser::creates([
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

    public function testRelationOfRoles()
    {
        /** @var AdminUser $admin */
        $admin = AdminUser::factory()->create();

        $role1 = AdminRole::factory()->create(['name' => 'role1']);
        $role2 = AdminRole::factory()->create(['name' => 'role2']);

        $admin->assignRole([$role1, $role2]);

        self::assertArrayHasKey('roles', $admin->toArray());
        self::assertCount(2, $admin['roles']);
        self::assertEquals('role1', $admin['roles'][0]['name']);

        $admins = AdminUser::all()->toArray();

        self::assertArrayHasKey('roles', $admins[0]);
        self::assertCount(2, $admins[0]['roles']);
        self::assertEquals('role1', $admins[0]['roles'][0]['name']);
    }
}
