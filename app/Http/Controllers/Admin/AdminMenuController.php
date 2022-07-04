<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminMenuRequest;
use App\Models\AdminMenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @title 后台菜单管理
 */
class AdminMenuController extends Controller
{
    /**
     * @title 获取菜单列表
     *
     * @param AdminMenu $adminMenu
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(AdminMenu $adminMenu, Request $request): JsonResponse
    {
        $adminMenu = $adminMenu->orderByDesc('sort');

        $request->has('public') and $adminMenu = $adminMenu->where('public', $request->boolean('public'));

        $menus = $adminMenu->with('permission')->get()->toTree();

        return success('获取菜单列表成功', $menus);
    }

    /**
     * @title 新增后台菜单
     *
     * @param AdminMenuRequest $request
     * @param AdminMenu $adminMenu
     *
     * @return JsonResponse
     */
    public function store(AdminMenuRequest $request, AdminMenu $adminMenu): JsonResponse
    {
        if ($parentId = $request->input('parent_id')) {
            $parentMenu = AdminMenu::finds($parentId);
            $adminMenu = AdminMenu::create($adminMenu->sanitize($request), $parentMenu);
        } else {
            $adminMenu = AdminMenu::create($adminMenu->sanitize($request));
        }

        return success('新增菜单成功', $adminMenu);
    }

    /**
     * @title 更新后台菜单
     *
     * @param AdminMenuRequest $request
     * @param AdminMenu $adminMenu
     *
     * @return JsonResponse
     */
    public function update(AdminMenuRequest $request, AdminMenu $adminMenu): JsonResponse
    {
        $adminMenu->sanitizeFill($request)->save();

        if (($parentId = $request->input('parent_id')) !== $adminMenu['parent_id']) {
            if (empty($parentId)) {
                $adminMenu->saveAsRoot();
            } else {
                $adminMenu->appendToNode(AdminMenu::finds($parentId))->save();
            }
        }

        return success('更新菜单成功', $adminMenu);
    }

    /**
     * @title 获取后台菜单详情
     *
     * @param AdminMenu $adminMenu
     *
     * @return JsonResponse
     */
    public function show(AdminMenu $adminMenu): JsonResponse
    {
        $adminMenu = $adminMenu->fresh(['permission']);
        return success('获取详情成功', $adminMenu);
    }

    /**
     * @title 删除后台菜单
     *
     * @param AdminMenu $adminMenu
     *
     * @return JsonResponse
     */
    public function destroy(AdminMenu $adminMenu): JsonResponse
    {
        return $adminMenu->delete() ? success('删除成功') : error('删除失败');
    }
}
