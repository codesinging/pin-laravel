<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SettingGroupRequest;
use App\Models\SettingGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

/**
 * @title 设置分组管理
 * @permission
 */
class SettingGroupController extends Controller
{
    /**
     * @title 获取设置分组列表
     *
     * @param SettingGroup $settingGroup
     *
     * @return JsonResponse
     */
    public function index(SettingGroup $settingGroup): JsonResponse
    {
        $lister = $settingGroup->lister(fn(Builder $builder) => $builder->latest('sort'));

        return success('获取设置分组列表成功', $lister);
    }

    /**
     * @title 新增设置分组
     *
     * @param SettingGroup $settingGroup
     * @param SettingGroupRequest $request
     *
     * @return JsonResponse
     */
    public function store(SettingGroup $settingGroup, SettingGroupRequest $request): JsonResponse
    {
        $request->validate([
            'key' => 'unique:' . $settingGroup->getTable(),
        ], [], $request->attributes());

        return $settingGroup->sanitizeFill($request)->save()
            ? success('新增分组成功', $settingGroup)
            : error('新增分组失败');
    }

    /**
     * @title 更新设置分组
     *
     * @param SettingGroup $settingGroup
     * @param SettingGroupRequest $request
     *
     * @return JsonResponse
     */
    public function update(SettingGroup $settingGroup, SettingGroupRequest $request): JsonResponse
    {
        $request->validate([
            'key' => Rule::unique($settingGroup->getTable())->ignore($settingGroup),
        ], [], $request->attributes());

        return $settingGroup->sanitizeFill($request)->save()
            ? success('更新分组成功', $settingGroup)
            : error('更新分组失败');
    }

    /**
     * @title 获取设置分组详情
     *
     * @param SettingGroup $settingGroup
     *
     * @return JsonResponse
     */
    public function show(SettingGroup $settingGroup): JsonResponse
    {
        return success('获取设置分组详情成功', $settingGroup);
    }

    /**
     * @title 删除设置分组
     *
     * @param SettingGroup $settingGroup
     *
     * @return JsonResponse
     */
    public function destroy(SettingGroup $settingGroup): JsonResponse
    {
        return $settingGroup->delete()
            ? success('删除设置分组成功', $settingGroup)
            : error('删除设置分组失败');
    }
}
