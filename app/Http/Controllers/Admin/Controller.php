<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Models\AdminUser;
use App\Support\Routing\BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    /**
     * 返回当前登录的用户
     *
     * @return AdminUser|null
     */
    protected function authUser(): ?AdminUser
    {
        /** @var AdminUser $user */
        $user = Auth::user();

        return $user;
    }
}
