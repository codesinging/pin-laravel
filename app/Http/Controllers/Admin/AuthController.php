<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Exceptions\AdminErrors;
use App\Models\Administrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * 用户登录
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

        /** @var Administrator $admin */
        $admin = Administrator::wheres('username', $request->input('username'))->first();

        if (empty($admin)) {
            return error(AdminErrors::AuthUserNotFound);
        }

        if (!Hash::check($request->input('password'), $admin['password'])) {
            return error(AdminErrors::AuthNotMatched);
        }

        if (!$admin['status']) {
            return error(AdminErrors::AuthInvalidStatus);
        }

        $token = $admin->createToken($request->input('device', ''))->plainTextToken;

        return success('登录成功', compact('admin', 'token'));
    }

    /**
     * 注销登录
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var Administrator $admin */
        $admin = $request->user();

        $admin->tokens()->where('tokenable_id', $admin['id'])->delete();

        return success('注销登录成功');
    }

    /**
     * 获取登录用户
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
     * 修改个人信息
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        /** @var Administrator $admin */
        $admin = $request->user();

        $admin->update($admin->sanitize($request));

        return success('修改个人信息成功', $admin);
    }

    /**
     * 修改密码
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function password(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password:sanctum'],
            'password' => ['required', 'confirmed'],
        ], [], [
            'current_password' => '当前密码',
            'password' => '新密码',
        ]);

        /** @var Administrator $admin */
        $admin = $request->user();

        return $admin->update(['password' => $request->input('password')])
            ? success('修改密码成功')
            : error('修改密码失败');
    }
}
