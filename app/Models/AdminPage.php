<?php

namespace App\Models;

use App\Events\AdminPageCreated;
use App\Events\AdminPageDeleted;
use App\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property AdminPermission $permission
 */
class AdminPage extends BaseModel
{
    public string $guard_name = 'sanctum';

    protected $fillable = [
        'name',
        'path',
        'sort',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'created' => AdminPageCreated::class,
        'deleted' => AdminPageDeleted::class,
    ];

    public function permission(): MorphOne
    {
        return $this->morphOne(AdminPermission::class, 'permissionable');
    }
}
