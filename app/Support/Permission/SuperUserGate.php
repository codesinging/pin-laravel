<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Permission;

use App\Support\Model\UserModel;
use Illuminate\Support\Facades\Gate;

trait SuperUserGate
{
    protected function superUserGate(): void
    {
        Gate::before(function (UserModel|IsSuper $user) {
            return $user->isSuper() ? true : null;
        });
    }
}
