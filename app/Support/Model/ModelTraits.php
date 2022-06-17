<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\Builder as Query;
use Kra8\Snowflake\HasShortflakePrimary;

/**
 * @mixin Builder
 * @mixin Query
 */
trait ModelTraits
{
    use HasFactory;

    use Instance;
    use SerializeDate;
    use Lister;
    use Sanitize;
    use StaticMethods;
}
