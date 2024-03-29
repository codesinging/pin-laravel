<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Middleware;

use App\Enums\AdminErrors;
use App\Models\AdminRoute;
use App\Models\AdminUser;
use Illuminate\Http\Request;

class AdminPermission
{
    public function handle(Request $request, \Closure $next)
    {
        /** @var AdminUser $user */
        $user = $request->user();

        $adminRoute = AdminRoute::findBy($request->route());

        if ($adminRoute && $adminRoute->permission){
            if ($user->cannot($adminRoute->permission['name'])){
                abort(403, AdminErrors::NoPermission->description());
            }
        }

        return $next($request);
    }
}
