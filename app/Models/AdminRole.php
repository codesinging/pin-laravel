<?php

namespace App\Models;

use App\Support\Model\ModelTraits;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

/**
 * @property Collection|AdminPermission[] $permissions;
 */
class AdminRole extends Role
{
    use ModelTraits;

    protected string $guard_name = 'sanctum';

    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'sort',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
