<?php

namespace App\Models;

use App\Support\Model\UserModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Traits\HasRoles;

class Administrator extends UserModel
{
    use HasRoles;

    protected string $guard_name = 'sanctum';

    protected $fillable = [
        'username',
        'name',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $with = [
        'roles',
    ];

    protected function password(): Attribute
    {
        return new Attribute(set: fn($value) => bcrypt($value));
    }
}
