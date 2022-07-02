<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminPageRequest;
use App\Models\AdminPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @title 后台页面管理
 */
class AdminPageController extends Controller
{
    /**
     * @title 获取后台页面列表
     *
     * @param AdminPage $adminPage
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(AdminPage $adminPage, Request $request): JsonResponse
    {
        $lister = $adminPage->lister(function (Builder $builder) use ($request) {
            $builder->orderByDesc('sort');
            $request->has('public') and $builder->where('public', $request->boolean('public'));
        });

        return success('获取页面列表成功', $lister);
    }

    /**
     * @title 新增后台页面
     *
     * @param AdminPageRequest $request
     * @param AdminPage $adminPage
     *
     * @return JsonResponse
     */
    public function store(AdminPageRequest $request, AdminPage $adminPage): JsonResponse
    {
        $request->validate([
            'path' => 'unique:' . $adminPage->getTable(),
        ], [], $request->attributes());

        return $adminPage->sanitizeFill($request)->save()
            ? success('新增成功', $adminPage)
            : error('新增失败');
    }

    /**
     * @title 更新后台页面
     *
     * @param AdminPage $adminPage
     * @param AdminPageRequest $request
     *
     * @return JsonResponse
     */
    public function update(AdminPage $adminPage, AdminPageRequest $request): JsonResponse
    {
        $request->validate([
            'path' => Rule::unique($adminPage->getTable())->ignore($adminPage),
        ], [], $request->attributes());

        return $adminPage->sanitizeFill($request)->save()
            ? success('更新成功', $adminPage)
            : error('更新失败');
    }

    /**
     * @title 获取后台页面详情
     *
     * @param AdminPage $adminPage
     *
     * @return JsonResponse
     */
    public function show(AdminPage $adminPage): JsonResponse
    {
        return success('获取后台页面详情成功', $adminPage);
    }

    /**
     * @title 删除后台页面
     *
     * @param AdminPage $adminPage
     *
     * @return JsonResponse
     */
    public function destroy(AdminPage $adminPage): JsonResponse
    {
        return $adminPage->delete()
            ? success('删除成功', $adminPage)
            : error('删除失败');
    }
}
