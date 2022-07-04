<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Models;

use App\Support\Model\BaseModel;
use App\Support\Model\ModelTraits;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Permission\Models\Permission;

/**
 * @property BaseModel $permissionable
 */
class AdminPermission extends Permission
{
    use ModelTraits;

    public string $guard_name = 'sanctum';

    protected $with = [
        'permissionable',
    ];

    public function permissionable(): MorphTo
    {
        return $this->morphTo();
    }
}
