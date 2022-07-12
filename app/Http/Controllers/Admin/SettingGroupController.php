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

/**
 * @title 配置分组管理
 * @permission
 */
class SettingGroupController extends Controller
{
    /**
     * @title 获取配置分组列表
     *
     * @param SettingGroup $settingGroup
     *
     * @return JsonResponse
     */
    public function index(SettingGroup $settingGroup): JsonResponse
    {
        $lister = $settingGroup->lister(fn(Builder $builder) => $builder->latest('sort'));

        return success('获取配置分组列表成功', $lister);
    }

    /**
     * @title 新增配置分组
     *
     * @param SettingGroup $settingGroup
     * @param SettingGroupRequest $request
     *
     * @return JsonResponse
     */
    public function store(SettingGroup $settingGroup, SettingGroupRequest $request): JsonResponse
    {
        return $settingGroup->sanitizeFill($request)->save()
            ? success('新增分组成功', $settingGroup)
            : error('新增分组失败');
    }

    /**
     * @title 更新配置分组
     *
     * @param SettingGroup $settingGroup
     * @param SettingGroupRequest $request
     *
     * @return JsonResponse
     */
    public function update(SettingGroup $settingGroup, SettingGroupRequest $request): JsonResponse
    {
        return $settingGroup->sanitizeFill($request)->save()
            ? success('更新分组成功', $settingGroup)
            : error('更新分组失败');
    }

    /**
     * @title 获取分组详情
     *
     * @param SettingGroup $settingGroup
     *
     * @return JsonResponse
     */
    public function show(SettingGroup $settingGroup): JsonResponse
    {
        return success('获取配置分组详情成功', $settingGroup);
    }

    /**
     * @title 删除分组
     *
     * @param SettingGroup $settingGroup
     *
     * @return JsonResponse
     */
    public function destroy(SettingGroup $settingGroup): JsonResponse
    {
        return $settingGroup->delete()
            ? success('删除分组成功', $settingGroup)
            : error('删除失败');
    }
}
