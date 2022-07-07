<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Models\AdminAction;
use App\Support\Routing\Router;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Route;
use ReflectionException;

/**
 * @title 后台动作管理
 * @permission
 */
class AdminActionController extends Controller
{
    /**
     * @title 获取动作列表
     * @permission
     *
     * @param AdminAction $adminAction
     *
     * @return JsonResponse
     */
    public function index(AdminAction $adminAction): JsonResponse
    {
        $lister = $adminAction->lister(fn(Builder $builder) => $builder->with('permission'));

        return success('获取动作列表成功', $lister);
    }

    /**
     * @title 获取动作详情
     *
     * @param AdminAction $adminAction
     *
     * @return JsonResponse
     */
    public function show(AdminAction $adminAction): JsonResponse
    {
        $adminAction = $adminAction->fresh(['permission']);
        return success('获取动作详情成功', $adminAction);
    }

    /**
     * @title 删除动作
     *
     * @param AdminAction $adminAction
     *
     * @return JsonResponse
     */
    public function destroy(AdminAction $adminAction): JsonResponse
    {
        return $adminAction->delete()
            ? success('删除动作成功', $adminAction)
            : error('删除动作失败');
    }

    /**
     * @title 同步动作
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function sync(): JsonResponse
    {
        Router::routes('api/admin')->each(fn(Route $route) => AdminAction::syncFrom($route));

        return success('同步动作成功');
    }
}
