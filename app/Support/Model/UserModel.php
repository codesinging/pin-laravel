<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\Builder as Query;
use Illuminate\Foundation\Auth\User;
use Kra8\Snowflake\HasShortflakePrimary;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin Builder
 * @mixin Query
 */
class UserModel extends User
{
    use HasApiTokens;
    use HasFactory;

    use HasShortflakePrimary;

    use Instance;
    use SerializeDate;
    use Lister;
    use Sanitize;
    use StaticMethods;
}
