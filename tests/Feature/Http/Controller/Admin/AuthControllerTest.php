<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Exceptions\AdminErrors;
use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLogin()
    {
        $this->putJson('api/admin/auth/login')
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['username', 'password']]);

        $this->putJson('api/admin/auth/login', ['username' => 'admin', 'password' => 'admin.123'])
            ->assertJsonPath('code', AdminErrors::AuthUserNotFound->value)
            ->assertJsonPath('message', AdminErrors::AuthUserNotFound->label())
            ->assertOk();

        $admin = Administrator::factory()->create([
            'username' => 'admin',
            'password' => 'admin.123',
            'status' => false,
        ]);

        $this->putJson('api/admin/auth/login', ['username' => 'admin', 'password' => 'admin.111'])
            ->assertJsonPath('code', AdminErrors::AuthNotMatched->value)
            ->assertJsonPath('message', AdminErrors::AuthNotMatched->label())
            ->assertOk();

        $this->putJson('api/admin/auth/login', ['username' => 'admin', 'password' => 'admin.123'])
            ->assertJsonPath('code', AdminErrors::AuthInvalidStatus->value)
            ->assertJsonPath('message', AdminErrors::AuthInvalidStatus->label())
            ->assertOk();

        $admin->update(['status' => true]);

        $this->putJson('api/admin/auth/login', ['username' => 'admin', 'password' => 'admin.123'])
            ->assertJsonPath('code', 0)
            ->assertJsonPath('data.admin.username', 'admin')
            ->assertJsonStructure(['code', 'message', 'data' => ['admin', 'token']])
            ->assertOk();
    }

    public function testLogout()
    {
        $admin = Administrator::factory()->create(['password' => 'admin.123', 'status' => true]);

        $this->putJson('api/admin/auth/login', ['username' => $admin['username'], 'password' => 'admin.123'])
            ->assertOk();

        Auth::login($admin);

        /** @var Administrator $user */
        $user = Auth::user();

        self::assertEquals(1, $user->tokens()->get()->count());

        $this->actingAs($admin)
            ->putJson('api/admin/auth/logout')
            ->assertOk();

        self::assertEquals(0, $user->tokens()->get()->count());
    }

    public function testUser()
    {
        $admin = Administrator::factory()->create();

        $this->actingAs($admin)
            ->getJson('api/admin/auth/user')
            ->assertJsonPath('data.name', $admin['name'])
            ->assertOk();
    }

    public function testUpdate()
    {
        $admin = Administrator::factory()->create(['password' => 'admin.123', 'status' => true]);

        $this->actingAs($admin)
            ->putJson('api/admin/auth/update', ['name' => 'test_name'])
            ->assertJsonPath('data.name', 'test_name')
            ->assertOk();

        $admin->refresh();

        self::assertEquals('test_name', $admin['name']);
    }

    public function testPassword()
    {
        $admin = Administrator::factory()->create(['password' => 'admin.123', 'status' => true]);

        $this->actingAs($admin)
            ->putJson('api/admin/auth/password')
            ->assertJsonValidationErrors(['password' => ['新密码 不能为空。']])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->putJson('api/admin/auth/password', ['password' => '123'])
            ->assertJsonValidationErrors(['password' => ['新密码 两次输入不一致。']])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->putJson('api/admin/auth/password', ['password' => '123', 'password_confirmation' => '1234'])
            ->assertJsonValidationErrors(['password' => ['新密码 两次输入不一致。']])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->putJson('api/admin/auth/password', ['password' => '123', 'password_confirmation' => '123'])
            ->assertJsonValidationErrors(['current_password' => ['当前密码 不能为空。']])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->putJson('api/admin/auth/password', ['password' => '123', 'password_confirmation' => '123', 'current_password' => 'admin'])
            ->assertJsonValidationErrors(['current_password' => ['密码错误。']])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->putJson('api/admin/auth/password', ['password' => '123', 'password_confirmation' => '123', 'current_password' => 'admin.123'])
            ->assertOk();

        $admin->refresh();

        self::assertTrue(Hash::check('123', $admin['password']));
    }
}
