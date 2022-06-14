<?php

namespace App\Models;

use App\Support\Model\UserModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Administrator extends UserModel
{
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

    protected function password(): Attribute
    {
        return new Attribute(set: fn($value) => bcrypt($value));
    }
}
