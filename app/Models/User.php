<?php

namespace App\Models;

use App\Support\Model\UserModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends UserModel
{
    protected $fillable = [
        'name',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected function password(): Attribute
    {
        return new Attribute(
            set: fn($value) => bcrypt($value)
        );
    }
}
