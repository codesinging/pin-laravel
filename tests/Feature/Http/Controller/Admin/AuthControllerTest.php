<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Enums\AdminErrors;
use App\Models\AdminLog;
use App\Models\AdminMenu;
use App\Models\AdminPage;
use App\Models\AdminRole;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mews\Captcha\Facades\Captcha;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testConfig()
    {
        config(['admin.captcha_enabled' => false]);

        $this->getJson('api/admin/auth/config')
            ->assertJsonPath('data.captchaEnabled', false)
            ->assertOk();

        config(['admin.captcha_enabled' => true]);

        $this->getJson('api/admin/auth/config')
            ->assertJsonPath('data.captchaEnabled', true)
            ->assertOk();
    }

    public function testLogin()
    {
        config(['admin.captcha_enabled' => false]);

        $this->putJson('api/admin/auth/login')
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['username', 'password']]);

        $this->putJson('api/admin/auth/login', ['username' => 'admin', 'password' => 'admin.123'])
            ->assertJsonPath('code', AdminErrors::AuthUserNotFound->value)
            ->assertJsonPath('message', AdminErrors::AuthUserNotFound->description())
            ->assertOk();

        $admin = AdminUser::factory()->create([
            'username' => 'admin',
            'password' => 'admin.123',
            'status' => false,
        ]);

        $this->putJson('api/admin/auth/login', ['username' => 'admin', 'password' => 'admin.111'])
            ->assertJsonPath('code', AdminErrors::AuthNotMatched->value)
            ->assertJsonPath('message', AdminErrors::AuthNotMatched->description())
            ->assertOk();

        $this->putJson('api/admin/auth/login', ['username' => 'admin', 'password' => 'admin.123'])
            ->assertJsonPath('code', AdminErrors::AuthInvalidStatus->value)
            ->assertJsonPath('message', AdminErrors::AuthInvalidStatus->description())
            ->assertOk();

        $admin->update(['status' => true]);

        $this->freezeSecond();

        $this->putJson('api/admin/auth/login', ['username' => 'admin', 'password' => 'admin.123'])
            ->assertJsonPath('code', 0)
            ->assertJsonPath('data.admin.username', 'admin')
            ->assertJsonStructure(['code', 'message', 'data' => ['admin', 'token']])
            ->assertOk();

        $admin->refresh();

        self::assertEquals(1, $admin['login_count']);
        self::assertEquals(now(), $admin['last_login_time']);
        self::assertEquals(request()->ip(), $admin['last_login_ip']);

        AdminUser::factory()->create(['username' => 'test', 'password' => '1']);

        config(['admin.login_error_limit' => 3]);

        $this->putJson('api/admin/auth/login', ['username' => 'test', 'password' => '2'])
            ->assertJsonPath('code', AdminErrors::AuthNotMatched->value)
            ->assertJsonPath('message', AdminErrors::AuthNotMatched->description())
            ->assertJsonPath('data.error_count', 1)
            ->assertOk();

        $this->putJson('api/admin/auth/login', ['username' => 'test', 'password' => '2'])
            ->assertJsonPath('code', AdminErrors::AuthNotMatched->value)
            ->assertJsonPath('message', AdminErrors::AuthNotMatched->description())
            ->assertJsonPath('data.error_count', 2)
            ->assertOk();

        $this->putJson('api/admin/auth/login', ['username' => 'test', 'password' => '2'])
            ->assertJsonPath('code', AdminErrors::AuthNotMatched->value)
            ->assertJsonPath('message', AdminErrors::AuthNotMatched->description())
            ->assertJsonPath('data.error_count', 3)
            ->assertOk();

        $this->putJson('api/admin/auth/login', ['username' => 'test', 'password' => '2'])
            ->assertJsonPath('code', AdminErrors::AuthLoginErrorLimit->value)
            ->assertJsonPath('message', AdminErrors::AuthLoginErrorLimit->description())
            ->assertJsonPath('data.error_count', 3)
            ->assertOk();
    }

    public function testLoginWithCaptcha()
    {
        config(['admin.captcha_enabled' => true]);

        $this->putJson('api/admin/auth/login', ['username' => 'admin', 'password' => 'admin.123'])
            ->assertJsonValidationErrors(['captcha'])
            ->assertStatus(422);
    }

    public function testLogout()
    {
        $admin = AdminUser::factory()->create(['password' => 'admin.123', 'status' => true]);

        config(['admin.captcha_enabled' => false]);

        $this->putJson('api/admin/auth/login', ['username' => $admin['username'], 'password' => 'admin.123'])
            ->assertOk();

        Auth::login($admin);

        /** @var AdminUser $user */
        $user = Auth::user();

        self::assertEquals(1, $user->tokens()->get()->count());

        $this->actingAs($admin)
            ->putJson('api/admin/auth/logout')
            ->assertOk();

        self::assertEquals(0, $user->tokens()->get()->count());
    }

    public function testUser()
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin)
            ->getJson('api/admin/auth/user')
            ->assertJsonPath('data.name', $admin['name'])
            ->assertOk();
    }

    public function testUpdate()
    {
        $admin = AdminUser::factory()->create(['password' => 'admin.123', 'status' => true]);

        $this->actingAs($admin)
            ->putJson('api/admin/auth/update', ['name' => 'test_name'])
            ->assertJsonPath('data.name', 'test_name')
            ->assertOk();

        $admin->refresh();

        self::assertEquals('test_name', $admin['name']);
    }

    public function testPassword()
    {
        $admin = AdminUser::factory()->create(['password' => 'admin.123', 'status' => true]);

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
            ->putJson('api/admin/auth/password', ['password' => 'admin.123', 'password_confirmation' => 'admin.123', 'current_password' => 'admin.123'])
            ->assertJsonValidationErrors(['password' => ['新密码 和 当前密码 必须不同。']])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->putJson('api/admin/auth/password', ['password' => '123', 'password_confirmation' => '123', 'current_password' => 'admin.123'])
            ->assertOk();

        $admin->refresh();

        self::assertTrue(Hash::check('123', $admin['password']));
    }

    public function testPages()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        /** @var AdminUser $user3 */
        $user3 = AdminUser::factory()->create();

        /** @var AdminUser $user4 */
        $user4 = AdminUser::factory()->create(['super' => true]);

        /** @var AdminUser $user5 */
        $user5 = AdminUser::factory()->create();

        /** @var AdminRole $role1 */
        $role1 = AdminRole::factory()->create();

        /** @var AdminRole $role2 */
        $role2 = AdminRole::factory()->create();

        /** @var AdminPage $page1 */
        $page1 = AdminPage::factory()->create();

        /** @var AdminPage $page2 */
        $page2 = AdminPage::factory()->create();

        /** @var AdminPage $page3 */
        $page3 = AdminPage::factory()->create();

        /** @var AdminPage $page4 */
        $page4 = AdminPage::factory()->create(['public' => true]);

        /** @var AdminPage $page5 */
        $page5 = AdminPage::factory()->create(['status' => false]);

        $user1->givePermissionTo($page1->permission);
        $user2->givePermissionTo($page1->permission, $page2->permission, $page5->permission);

        $role1->givePermissionTo($page1->permission);

        $user5->assignRole($role1, $role2);
        $user5->givePermissionTo($page3->permission, $page5->permission);

        $this->actingAs($user1)
            ->getJson('api/admin/auth/pages')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.*.id', [$page4['id'], $page1['id']])
            ->assertOk();

        $this->actingAs($user2)
            ->getJson('api/admin/auth/pages')
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.*.id', [$page4['id'], $page1['id'], $page2['id']])
            ->assertOk();

        $this->actingAs($user3)
            ->getJson('api/admin/auth/pages')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.*.id', [$page4['id']])
            ->assertOk();

        $this->actingAs($user4)
            ->getJson('api/admin/auth/pages')
            ->assertJsonCount(4, 'data')
            ->assertOk();

        $this->actingAs($user5)
            ->getJson('api/admin/auth/pages')
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.*.id', [$page4['id'], $page1['id'], $page3['id']])
            ->assertOk();
    }

    public function testMenus()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        /** @var AdminUser $user3 */
        $user3 = AdminUser::factory()->create();

        /** @var AdminUser $user4 */
        $user4 = AdminUser::factory()->create(['super' => true]);

        /** @var AdminMenu $menu1 */
        $menu1 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu2 */
        $menu2 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu3 */
        $menu3 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu4 */
        $menu4 = AdminMenu::factory()->create(['public' => true]);

        /** @var AdminMenu $menu5 */
        $menu5 = AdminMenu::factory()->create(['status' => false]);

        $user1->givePermissionTo($menu1->permission);
        $user2->givePermissionTo($menu1->permission, $menu2->permission, $menu5->permission);

        $this->actingAs($user1)
            ->getJson('api/admin/auth/menus')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.*.id', [$menu4['id'], $menu1['id']])
            ->assertOk();

        $this->actingAs($user2)
            ->getJson('api/admin/auth/menus')
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.*.id', [$menu4['id'], $menu1['id'], $menu2['id']])
            ->assertOk();

        $this->actingAs($user3)
            ->getJson('api/admin/auth/menus')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.*.id', [$menu4['id']])
            ->assertOk();

        $this->actingAs($user4)
            ->getJson('api/admin/auth/menus')
            ->assertJsonCount(4, 'data')
            ->assertOk();
    }

    public function testPermissions()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        /** @var AdminUser $user3 */
        $user3 = AdminUser::factory()->create();

        /** @var AdminUser $user4 */
        $user4 = AdminUser::factory()->create(['super' => true]);

        /** @var AdminRole $role1 */
        $role1 = AdminRole::factory()->create();

        /** @var AdminRole $role2 */
        $role2 = AdminRole::factory()->create();

        /** @var AdminPage $page1 */
        $page1 = AdminPage::factory()->create();

        /** @var AdminPage $page2 */
        $page2 = AdminPage::factory()->create();

        /** @var AdminPage $page3 */
        $page3 = AdminPage::factory()->create();

        /** @var AdminPage $page4 */
        $page4 = AdminPage::factory()->create(['public' => true]);

        /** @var AdminMenu $menu1 */
        $menu1 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu2 */
        $menu2 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu3 */
        $menu3 = AdminMenu::factory()->create();

        /** @var AdminMenu $menu4 */
        $menu4 = AdminMenu::factory()->create(['public' => true]);

        $user1->assignRole($role1);
        $user2->assignRole($role2);

        $role1->givePermissionTo($page1->permission);
        $role2->givePermissionTo($page2->permission, $menu1->permission, $menu2->permission);

        $user1->givePermissionTo($page3->permission);
        $user2->givePermissionTo($menu3->permission);

        $this->actingAs($user1)
            ->getJson('api/admin/auth/permissions')
            ->assertJsonCount(2, 'data')
            ->assertOk();

        $this->actingAs($user2)
            ->getJson('api/admin/auth/permissions')
            ->assertJsonCount(4, 'data')
            ->assertOk();
    }

    public function testLogs()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        AdminLog::factory()->count(5)->create(['user_id' => $user1['id']]);
        AdminLog::factory()->count(3)->create(['user_id' => $user2['id']]);

        $this->actingAs($user1)
            ->getJson('api/admin/auth/logs')
            ->assertJsonCount(5, 'data')
            ->assertOk();

        $this->actingAs($user2)
            ->getJson('api/admin/auth/logs')
            ->assertJsonCount(3, 'data')
            ->assertOk();
    }

    public function testLogins()
    {
        /** @var AdminUser $user1 */
        $user1 = AdminUser::factory()->create();

        /** @var AdminUser $user2 */
        $user2 = AdminUser::factory()->create();

        $user1->login('1', false, 1, 'msg');
        $user1->login('1', false, 1, 'msg');
        $user2->login('1', false, 1, 'msg');

        $this->actingAs($user1)
            ->getJson('api/admin/auth/logins')
            ->assertJsonCount(2, 'data')
            ->assertOk();

        $this->actingAs($user2)
            ->getJson('api/admin/auth/logins')
            ->assertJsonCount(1, 'data')
            ->assertOk();
    }

    public function testLastLogin()
    {
        /** @var AdminUser $user */
        $user = AdminUser::factory()->create();

        $this->actingAs($user)
            ->getJson('api/admin/auth/last_login')
            ->assertJsonPath('data', null)
            ->assertOk();

        $login1 = $user->login('1', false, 1, 'msg');

        $this->actingAs($user)
            ->getJson('api/admin/auth/last_login')
            ->assertJsonPath('data', null)
            ->assertOk();

        $login2 = $user->login('2', false, 2, 'msg');

        $this->actingAs($user)
            ->getJson('api/admin/auth/last_login')
            ->assertJsonPath('data.id', $login1['id'])
            ->assertOk();

        $login3 = $user->login('3', false, 3, 'msg');
        $login4 = $user->login('4', false, 4, 'msg');

        $this->actingAs($user)
            ->getJson('api/admin/auth/last_login')
            ->assertJsonPath('data.id', $login3['id'])
            ->assertOk();
    }
}
