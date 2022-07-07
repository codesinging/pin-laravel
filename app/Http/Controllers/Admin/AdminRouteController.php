<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Models\AdminRoute;
use App\Support\Routing\Router;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use ReflectionException;

/**
 * @title 后台路由管理
 * @permission
 */
class AdminRouteController extends Controller
{
    /**
     * @title 获取路由列表
     * @permission
     *
     * @param AdminRoute $adminRoute
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(AdminRoute $adminRoute, Request $request): JsonResponse
    {
        $lister = $adminRoute->lister(function (Builder $builder) use ($request) {
            $builder->with('permission');

            $request->has('public') and $builder->where('public', $request->boolean('public'));
        });

        return success('获取路由列表成功', $lister);
    }

    /**
     * @title 获取路由详情
     *
     * @param AdminRoute $adminRoute
     *
     * @return JsonResponse
     */
    public function show(AdminRoute $adminRoute): JsonResponse
    {
        $adminRoute = $adminRoute->fresh(['permission']);
        return success('获取路由详情成功', $adminRoute);
    }

    /**
     * @title 删除路由
     *
     * @param AdminRoute $adminRoute
     *
     * @return JsonResponse
     */
    public function destroy(AdminRoute $adminRoute): JsonResponse
    {
        return $adminRoute->delete()
            ? success('删除路由成功', $adminRoute)
            : error('删除路由失败');
    }

    /**
     * @title 同步路由
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function sync(): JsonResponse
    {
        $routes = Router::routes('api/admin');

        AdminRoute::all()->each(function (AdminRoute $adminRoute) use ($routes){
            if (!Router::exists($adminRoute, $routes)){
                $adminRoute->delete();
            }
        });

        $routes->each(fn(Route $route) => AdminRoute::syncFrom($route));

        return success('同步路由成功');
    }
}
