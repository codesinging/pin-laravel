<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Models;

use Spatie\Permission\Models\Permission;

class AdminPermission extends Permission
{
    protected string $guard_name = 'sanctum';
}
