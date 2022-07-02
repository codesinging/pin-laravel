<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Exceptions\Errors;
use App\Http\Requests\AdminUserRequest;
use App\Models\AdminAction;
use App\Models\AdminMenu;
use App\Models\AdminPage;
use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * @title 管理员管理
 */
class AdminUserController extends Controller
{
    /**
     * @title 获取管理员列表
     *
     * @param AdminUser $adminUser
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(AdminUser $adminUser, Request $request): JsonResponse
    {
        $lister = $adminUser->lister(function (Builder $builder) use ($request, $adminUser) {
            if ($request->has('role')) {
                $builder = $adminUser::role($request->input('role'));
            }

            return $builder;
        });

        return success('获取管理员列表成功', $lister);
    }

    /**
     * @title 新增管理员
     *
     * @param AdminUserRequest $request
     * @param AdminUser $adminUser
     *
     * @return JsonResponse
     */
    public function store(AdminUserRequest $request, AdminUser $adminUser): JsonResponse
    {
        $request->validate([
            'password' => 'required',
            'username' => 'unique:' . $adminUser->getTable(),
            'name' => 'unique:' . $adminUser->getTable(),
        ], [], $request->attributes());

        return $adminUser->sanitizeFill($request)->save()
            ? success('新增成功', $adminUser)
            : error('新增失败');
    }

    /**
     * @title 更新管理员
     *
     * @param AdminUserRequest $request
     * @param AdminUser $adminUser
     *
     * @return JsonResponse
     */
    public function update(AdminUserRequest $request, AdminUser $adminUser): JsonResponse
    {
        if ($adminUser->isSuper()) {
            return error(Errors::Forbidden);
        }

        $request->validate([
            'username' => Rule::unique($adminUser->getTable())->ignore($adminUser),
            'name' => Rule::unique($adminUser->getTable())->ignore($adminUser),
        ], [], $request->attributes());

        return $adminUser->sanitizeFill($request)->save()
            ? success('更新成功', $adminUser)
            : error('更新失败');
    }

    /**
     * @title 获取管理员详情
     *
     * @param AdminUser $adminUser
     *
     * @return JsonResponse
     */
    public function show(AdminUser $adminUser): JsonResponse
    {
        return success('获取管理员详情成功', $adminUser);
    }

    /**
     * @title 删除管理员
     *
     * @param AdminUser $adminUser
     *
     * @return JsonResponse
     */
    public function destroy(AdminUser $adminUser): JsonResponse
    {
        if ($adminUser->isSuper()) {
            return error(Errors::Forbidden);
        }

        return $adminUser->delete() ? success('删除成功', $adminUser) : error('删除失败');
    }

    /**
     * @title 设置管理员权限
     *
     * @param AdminUser $adminUser
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function permit(AdminUser $adminUser, Request $request): JsonResponse
    {
        if ($adminUser->isSuper()) {
            return error(Errors::Forbidden);
        }

        $adminUser->syncPermissions(Arr::wrap($request->get('permissions', [])));

        return success('设置管理员权限成功');
    }

    /**
     * @title 指派管理员角色
     *
     * @param AdminUser $adminUser
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function assign(AdminUser $adminUser, Request $request): JsonResponse
    {
        if ($adminUser->isSuper()) {
            return error(Errors::Forbidden);
        }

        $adminUser->syncRoles(Arr::wrap($request->get('roles', [])));

        return success('指派管理员角色成功');
    }

    /**
     * @title 获取管理员权限页面
     *
     * @param AdminUser $adminUser
     *
     * @return JsonResponse
     */
    public function pages(AdminUser $adminUser): JsonResponse
    {
        if ($adminUser->isSuper()) {
            return error(Errors::Forbidden);
        }

        $permissionables = $adminUser->permissionables(AdminPage::class);

        return success('获取用户权限页面成功', $permissionables);
    }

    /**
     * @title 获取管理员权限菜单
     *
     * @param AdminUser $adminUser
     *
     * @return JsonResponse
     */
    public function menus(AdminUser $adminUser): JsonResponse
    {
        if ($adminUser->isSuper()) {
            return error(Errors::Forbidden);
        }

        $permissionables = $adminUser->permissionables(AdminMenu::class);

        return success('获取用户权限菜单成功', $permissionables);
    }

    /**
     * @title 获取管理员权限动作
     *
     * @param AdminUser $adminUser
     *
     * @return JsonResponse
     */
    public function actions(AdminUser $adminUser): JsonResponse
    {
        if ($adminUser->isSuper()) {
            return error(Errors::Forbidden);
        }

        $permissionables = $adminUser->permissionables(AdminAction::class);

        return success('获取用户权限动作成功', $permissionables);
    }

    /**
     * @title 获取管理员权限实体
     *
     * @param AdminUser $adminUser
     *
     * @return JsonResponse
     */
    public function permissionables(AdminUser $adminUser): JsonResponse
    {
        if ($adminUser->isSuper()) {
            return error(Errors::Forbidden);
        }

        $permissionables = $adminUser->permissionables();

        return success('获取用户权限实体成功', $permissionables);
    }

    /**
     * @title 获取用户权限
     *
     * @param AdminUser $adminUser
     *
     * @return JsonResponse
     */
    public function permissions(AdminUser $adminUser): JsonResponse
    {
        if ($adminUser->isSuper()) {
            return error(Errors::Forbidden);
        }

        $permissions = $adminUser->permissions;

        return success('获取用户权限成功', $permissions);
    }
}
