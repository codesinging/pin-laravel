<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Models\AdminLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    /**
     * @title 获取日志列表
     *
     * @param AdminLog $adminLog
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(AdminLog $adminLog, Request $request): JsonResponse
    {
        $lister = $adminLog->lister(function (Builder $builder) use ($request) {
            if ($request->has('method')) {
                $builder->where('method', $request->input('method'));
            }
            if ($request->has('user_id')) {
                $builder->where('user_id', $request->input('user_id'));
            }
            if ($request->has('status')) {
                $builder->where('status', $request->input('status'));
            }
            if ($request->has('code')) {
                $builder->where('code', $request->input('code'));
            }
            if ($request->has('start')) {
                $builder->where('created_at', '>=', $request->input('start'));
            }
            if ($request->has('end')) {
                $builder->where('created_at', '<=', $request->input('end'));
            }
        });

        return success('获取列表成功', $lister);
    }

    /**
     * @title 获取日志详情
     *
     * @param AdminLog $adminLog
     *
     * @return JsonResponse
     */
    public function show(AdminLog $adminLog): JsonResponse
    {
        return success('获取日志详情成功', $adminLog);
    }
}
