<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use Illuminate\Foundation\Auth\User;
use Laravel\Sanctum\HasApiTokens;

class UserModel extends User
{
    use HasApiTokens;

    use ModelTraits;
}
