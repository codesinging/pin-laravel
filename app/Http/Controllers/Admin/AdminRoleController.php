<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminRoleRequest;
use App\Models\AdminRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * @title 管理员角色管理
 */
class AdminRoleController extends Controller
{
    /**
     * @title 获取管理员角色列表
     *
     * @param AdminRole $adminRole
     *
     * @return JsonResponse
     */
    public function index(AdminRole $adminRole): JsonResponse
    {
        $lister = $adminRole->lister(function (Builder $builder) {
            $builder->orderByDesc('sort');
        });

        return success('获取角色列表成功', $lister);
    }

    /**
     * @title 新增管理员角色
     *
     * @param AdminRoleRequest $request
     * @param AdminRole $adminRole
     *
     * @return JsonResponse
     */
    public function store(AdminRoleRequest $request, AdminRole $adminRole): JsonResponse
    {
        $request->validate([
            'name' => 'unique:' . $adminRole->getTable(),
        ], [], $request->attributes());

        return $adminRole->sanitizeFill($request)->save()
            ? success('新增成功', $adminRole)
            : error('新增失败');
    }

    /**
     * @title 更新管理员角色
     *
     * @param AdminRoleRequest $request
     * @param AdminRole $adminRole
     *
     * @return JsonResponse
     */
    public function update(AdminRoleRequest $request, AdminRole $adminRole): JsonResponse
    {
        $request->validate([
            'name' => Rule::unique($adminRole->getTable())->ignore($adminRole),
        ], [], $request->attributes());

        return $adminRole->sanitizeFill($request)->save()
            ? success('更新成功', $adminRole)
            : error('更新失败');
    }

    /**
     * @title 获取管理员角色详情
     *
     * @param AdminRole $adminRole
     *
     * @return JsonResponse
     */
    public function show(AdminRole $adminRole): JsonResponse
    {
        return success('获取管理员角色详情成功', $adminRole);
    }

    /**
     * @title 删除管理员角色
     *
     * @param AdminRole $adminRole
     *
     * @return JsonResponse
     */
    public function destroy(AdminRole $adminRole): JsonResponse
    {
        return $adminRole->delete() ? success('删除成功', $adminRole) : error('删除失败');
    }

    /**
     * @title 设置角色权限
     *
     * @param AdminRole $adminRole
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function permit(AdminRole $adminRole, Request $request): JsonResponse
    {
        $adminRole->syncPermissions(Arr::wrap($request->get('permissions', [])));

        return success('设置权限成功');
    }
}
