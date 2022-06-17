<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Middleware;

use App\Exceptions\AdminErrors;
use App\Models\AdminAction;
use App\Models\AdminUser;
use Illuminate\Http\Request;

class AdminPermission
{
    public function handle(Request $request, \Closure $next)
    {
        /** @var AdminUser $user */
        $user = $request->user();

        $adminAction = AdminAction::findBy($request->route());

        if ($adminAction && $adminAction->permission){
            if ($user->cannot($adminAction->permission['name'])){
                abort(403, AdminErrors::NoPermission->label());
            }
        }

        return $next($request);
    }
}
