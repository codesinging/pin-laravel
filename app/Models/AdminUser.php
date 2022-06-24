<?php

namespace App\Models;

use App\Support\Model\UserModel;
use App\Support\Permission\IsSuper;
use Closure;
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
        'super',
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
     * 获取当前用户拥有权限的指定类型的模型列表
     *
     * @param string|array $types
     * @param Closure|null $callback
     *
     * @return \Illuminate\Database\Eloquent\Collection|array|Collection
     */
    public function permissionables(string|array $types = '*', Closure $callback = null): \Illuminate\Database\Eloquent\Collection|array|Collection
    {
        return $this->permissions()
            ->with('permissionable')
            ->whereHasMorph('permissionable', $types, $callback)
            ->get()
            ->map(fn(AdminPermission $permission) => $permission->permissionable);
    }
}
