<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @title 设置更新管理
 * @permission
 */
class SettingController extends Controller
{
    /**
     * @title 更新设置值
     *
     * @param Setting $setting
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update(Setting $setting, Request $request): JsonResponse
    {
        $setting['value'] = $request->input('value');

        return $setting->save()
            ? success('更新设置值成功', $setting)
            : error('更新设置值失败');
    }
}
