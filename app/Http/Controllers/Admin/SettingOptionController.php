<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Enums\SettingTypes;
use App\Http\Requests\SettingOptionRequest;
use App\Models\SettingOption;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @title 设置管理
 * @permission
 */
class SettingOptionController extends Controller
{
    /**
     * @title 获取设置列表
     *
     * @param SettingOption $settingOption
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(SettingOption $settingOption, Request $request): JsonResponse
    {
        $lister = $settingOption->lister(function (Builder $builder) use ($request) {
            $builder->latest('sort')->orderBy('id');
            $request->has('group_id') and $builder->where('group_id', $request->input('group_id'));
        });

        return success('获取设置列表成功', $lister);
    }

    /**
     * @title 新增设置
     *
     * @param SettingOption $settingOption
     * @param SettingOptionRequest $request
     *
     * @return JsonResponse
     */
    public function store(SettingOption $settingOption, SettingOptionRequest $request): JsonResponse
    {
        $request->validate([
            'key' => 'unique:' . $settingOption->getTable(),
        ], [], $request->attributes());

        return $settingOption->sanitizeFill($request)->save()
            ? success('新增设置成功', $settingOption)
            : error('新增设置失败');
    }

    /**
     * @title 更新设置
     *
     * @param SettingOption $settingOption
     * @param SettingOptionRequest $request
     *
     * @return JsonResponse
     */
    public function update(SettingOption $settingOption, SettingOptionRequest $request): JsonResponse
    {
        $request->validate([
            'key' => Rule::unique($settingOption->getTable())->ignore($settingOption),
        ], [], $request->attributes());

        return $settingOption->sanitizeFill($request)->save()
            ? success('更新设置成功', $settingOption)
            : error('更新设置失败');
    }

    /**
     * @title 获取设置详情
     *
     * @param SettingOption $settingOption
     *
     * @return JsonResponse
     */
    public function show(SettingOption $settingOption): JsonResponse
    {
        return success('获取设置详情成功', $settingOption);
    }

    /**
     * @title 删除设置
     *
     * @param SettingOption $settingOption
     *
     * @return JsonResponse
     */
    public function destroy(SettingOption $settingOption): JsonResponse
    {
        return $settingOption->delete()
            ? success('删除设置成功', $settingOption)
            : error('删除设置失败');
    }

    /**
     * @title 获取类型列表
     * @return JsonResponse
     */
    public function types(): JsonResponse
    {
        $types = SettingTypes::options();

        return success('获取设置类型列表成功', $types);
    }
}
