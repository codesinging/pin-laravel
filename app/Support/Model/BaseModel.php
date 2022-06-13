<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as Query;
use Kra8\Snowflake\HasShortflakePrimary;

/**
 * @mixin Builder
 * @mixin Query
 */
class BaseModel extends Model
{
    use HasFactory;

    use HasShortflakePrimary;

    use Instance;
    use SerializeDate;
    use Lister;
}
