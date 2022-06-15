<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Exceptions\Errors;
use App\Http\Requests\AdministratorRequest;
use App\Models\Administrator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class AdministratorController extends Controller
{
    /**
     * @title 获取管理员列表
     *
     * @param Administrator $administrator
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Administrator $administrator, Request $request): JsonResponse
    {
        $lister = $administrator->lister(function (Builder $builder) use ($request, $administrator) {
            if ($request->has('role')) {
                $builder = $administrator::role($request->input('role'));
            }

            return $builder;
        });

        return success('获取管理员列表成功', $lister);
    }

    /**
     * @title 新增管理员
     *
     * @param AdministratorRequest $request
     * @param Administrator $administrator
     *
     * @return JsonResponse
     */
    public function store(AdministratorRequest $request, Administrator $administrator): JsonResponse
    {
        $request->validate([
            'password' => 'required',
            'username' => 'unique:administrators',
            'name' => 'unique:administrators',
        ], [], $request->attributes());

        return $administrator->sanitizeFill($request)->save()
            ? success('新增成功', $administrator)
            : error('新增失败');
    }

    /**
     * @title 更新管理员
     *
     * @param AdministratorRequest $request
     * @param Administrator $administrator
     *
     * @return JsonResponse
     */
    public function update(AdministratorRequest $request, Administrator $administrator): JsonResponse
    {
        if ($administrator->isSuper()) {
            return error(Errors::Forbidden);
        }

        $request->validate([
            'username' => Rule::unique($administrator->getTable())->ignore($administrator),
            'name' => Rule::unique($administrator->getTable())->ignore($administrator),
        ], [], $request->attributes());

        return $administrator->sanitizeFill($request)->save()
            ? success('更新成功', $administrator)
            : error('更新失败');
    }

    /**
     * @title 获取管理员详情
     *
     * @param Administrator $administrator
     *
     * @return JsonResponse
     */
    public function show(Administrator $administrator): JsonResponse
    {
        return success('获取管理员详情成功', $administrator);
    }

    /**
     * @title 删除管理员
     *
     * @param Administrator $administrator
     *
     * @return JsonResponse
     */
    public function destroy(Administrator $administrator): JsonResponse
    {
        if ($administrator->isSuper()) {
            return error(Errors::Forbidden);
        }

        return $administrator->delete() ? success('删除成功', $administrator) : error('删除失败');
    }

    /**
     * @title 设置管理员权限
     *
     * @param Administrator $administrator
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function permit(Administrator $administrator, Request $request): JsonResponse
    {
        $administrator->syncPermissions(Arr::wrap($request->get('permissions', [])));

        return success('设置管理员权限成功');
    }

    /**
     * @title 指派管理员角色
     *
     * @param Administrator $administrator
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function assign(Administrator $administrator, Request $request): JsonResponse
    {
        $administrator->syncRoles(Arr::wrap($request->get('roles', [])));

        return success('指派管理员角色成功');
    }
}
