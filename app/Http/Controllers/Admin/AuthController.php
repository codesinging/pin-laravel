<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Enums\AdminErrors;
use App\Models\AdminLog;
use App\Models\AdminLogin;
use App\Models\AdminMenu;
use App\Models\AdminPage;
use App\Models\AdminUser;
use App\Support\Miscellaneous\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @title 管理员认证管理
 */
class AuthController extends Controller
{
    /**
     * @title 获取登录设置信息
     * @return JsonResponse
     */
    public function config(): JsonResponse
    {
        $captchaEnabled = Setting::get('admin_captcha_enabled', config('admin.captcha_enabled', false));

        return success('获取登录设置信息成功', compact('captchaEnabled'));
    }

    /**
     * @title 用户登录
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [], [
            'username' => '登录账号',
            'password' => '登录密码',
        ]);

        $captchaEnabled = config('admin.captcha_enabled', false);

        if ($captchaEnabled) {
            $request->validate([
                'captcha' => 'required|captcha_api:' . $request->input('key'),
            ], [
                'captcha.required' => '验证码不能为空',
                'captcha.captcha_api' => '验证码不正确',
            ]);
        }

        /** @var AdminUser $admin */
        $admin = AdminUser::wheres('username', $request->input('username'))->first();

        if (empty($admin)) {
            return error(AdminErrors::AuthUserNotFound);
        }

        $loginErrorLimit = config('admin.login_error_limit', 5);

        if ($admin['login_error_count'] >= $loginErrorLimit) {
            $admin->login($request->ip(), false, AdminErrors::AuthLoginErrorLimit->value, AdminErrors::AuthLoginErrorLimit->description());

            return error(AdminErrors::AuthLoginErrorLimit, 1, [
                'error_count' => $admin['login_error_count'],
                'error_limit' => $loginErrorLimit,
            ]);
        }

        if (!Hash::check($request->input('password'), $admin['password'])) {
            $admin->increment('login_error_count');

            $admin->login($request->ip(), false, AdminErrors::AuthNotMatched->value, AdminErrors::AuthNotMatched->description());

            return error(AdminErrors::AuthNotMatched, 1, [
                'error_count' => $admin['login_error_count'],
                'error_limit' => $loginErrorLimit,
            ]);
        }

        if (!$admin['status']) {
            $admin->login($request->ip(), false, AdminErrors::AuthInvalidStatus->value, AdminErrors::AuthInvalidStatus->description());

            return error(AdminErrors::AuthInvalidStatus);
        }

        $admin->update([
            'login_count' => $admin['login_count'] + 1,
            'last_login_time' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $admin->login($request->ip(), true, 0, '登录成功');

        $token = $admin->createToken($request->input('device', ''))->plainTextToken;

        return success('登录成功', compact('admin', 'token'));
    }

    /**
     * @title 注销登录
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $admin = $this->authUser();

        $admin?->tokens()->where('tokenable_id', $admin['id'])->delete();

        return success('注销登录成功');
    }

    /**
     * @title 获取登录用户
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return success('获取登录用户成功', $request->user());
    }

    /**
     * @title 修改个人信息
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $admin = $this->authUser();

        $admin->update($admin->sanitize($request));

        return success('修改个人信息成功', $admin);
    }

    /**
     * @title 修改密码
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function password(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password:sanctum'],
            'password' => ['required', 'confirmed', 'different:current_password'],
        ], [], [
            'current_password' => '当前密码',
            'password' => '新密码',
        ]);

        return $this->authUser()->update(['password' => $request->input('password')])
            ? success('修改密码成功')
            : error('修改密码失败');
    }

    /**
     * @title 获取登录用户拥有权限的页面列表
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function pages(Request $request): JsonResponse
    {
        $user = $this->authUser();

        if ($user->isSuper()) {
            $pages = AdminPage::wheres('status', true)->get();
        } else {
            $publicPages = AdminPage::wheres('public', true)->where('status', true)->get();
            $permissionablePages = $user->permissionables(AdminPage::class);
            $pages = $publicPages->concat($permissionablePages);
        }

        return success('获取页面列表成功', $pages);
    }

    /**
     * @title 获取登录用户拥有权限的菜单列表
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function menus(Request $request): JsonResponse
    {
        $user = $this->authUser();

        if ($user->isSuper()) {
            $menus = AdminMenu::wheres('status', true)->get();
        } else {
            $publicMenus = AdminMenu::wheres('status', true)->where('public', true)->get();
            $permissionableMenus = $user->permissionables(AdminMenu::class);
            $menus = $publicMenus->concat($permissionableMenus);
        }

        return success('获取菜单列表成功', $menus);
    }

    /**
     * @title 获取登录用户所有权限列表
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function permissions(Request $request): JsonResponse
    {
        $user = $this->authUser();

        $permissions = $user->getAllPermissions();

        return success('获取权限列表成功', $permissions);
    }

    /**
     * @title 获取登录用户操作日志列表
     *
     * @param Request $request
     * @param AdminLog $adminLog
     *
     * @return JsonResponse
     */
    public function logs(Request $request, AdminLog $adminLog): JsonResponse
    {
        $logs = $adminLog->lister(function () {
            return $this->authUser()->logs()->latest();
        });

        return success('获取操作日志列表成功', $logs);
    }

    /**
     * @title 获取登录用户登录日志列表
     *
     * @param Request $request
     * @param AdminLogin $adminLogin
     *
     * @return JsonResponse
     */
    public function logins(Request $request, AdminLogin $adminLogin): JsonResponse
    {
        $logins = $adminLogin->lister(function () {
            return $this->authUser()->logins()->latest();
        });

        return success('获取操作日志列表成功', $logins);
    }

    /**
     * @title 获取上一次登录日志
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function lastLogin(Request $request): JsonResponse
    {
        $login = $this->authUser()->logins()->latest('id')->offset(1)->first();

        return success('获取上一次登录日志成功', $login);
    }
}
