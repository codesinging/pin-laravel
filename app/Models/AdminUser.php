<?php

namespace App\Models;

use App\Support\Model\UserModel;
use App\Support\Permission\IsSuper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Collection;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property Collection|AdminPermission[] $permissions
 * @property Collection|AdminRole[] $roles
 *
 * @method static Builder role(string|int|array|Role|Collection $roles, string $guard = null)
 */
class AdminUser extends UserModel implements IsSuper
{
    use HasRoles;

    protected string $guard_name = 'sanctum';

    protected $fillable = [
        'username',
        'name',
        'password',
        'super',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'super' => 'boolean',
        'status' => 'boolean',
    ];

    protected $with = [
        'roles',
    ];

    protected function password(): Attribute
    {
        return new Attribute(set: fn($value) => bcrypt($value));
    }

    /**
     * 是否超级管理员
     *
     * @return boolean
     */
    public function isSuper(): bool
    {
        return (bool)$this->attributes['super'];
    }

    /**
     * 获取管理员权限父模型
     *
     * @param string|null $type
     * @param bool $status
     *
     * @return Collection
     */
    public function permissionables(string $type = null, bool $status = true): Collection
    {
        $permissions = $this->getAllPermissions();

        if (!empty($type)) {
            $permissions = $permissions->filter(fn(AdminPermission $permission) => $permission['permissionable_type'] === $type);
        }

        $permissionables = $permissions->map(fn(AdminPermission $permission) => $permission->permissionable);

        $permissionables = $permissionables->filter(fn($permissionable) => $permissionable['status'] === $status);

        return $permissionables->values();
    }
}
