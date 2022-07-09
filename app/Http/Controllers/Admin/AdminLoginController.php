<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Models\AdminLogin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @title 登录日志管理
 * @permission
 */
class AdminLoginController extends Controller
{
    /**
     * @title 获取日志列表
     *
     * @param AdminLogin $adminLogin
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(AdminLogin $adminLogin, Request $request): JsonResponse
    {
        $lister = $adminLogin->lister(function (Builder $builder) use ($request) {
            $builder->latest('id');

            if ($request->has('user_id')) {
                $builder->where('user_id', $request->input('user_id'));
            }
            if ($request->has('code')) {
                $builder->where('code', $request->input('code'));
            }
            if ($request->has('start')) {
                $builder->where('created_at', '>=', $request->input('start'));
            }
            if ($request->has('end')) {
                $builder->where('created_at', '<=', $request->input('end'));
            }
        });

        return success('获取列表成功', $lister);
    }

    /**
     * @title 获取登录日志详情
     *
     * @param AdminLogin $adminLogin
     *
     * @return JsonResponse
     */
    public function show(AdminLogin $adminLogin): JsonResponse
    {
        return success('获取登录日志详情成功', $adminLogin);
    }
}
