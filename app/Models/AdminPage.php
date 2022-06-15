<?php

namespace App\Models;

use App\Support\Model\BaseModel;

class AdminPage extends BaseModel
{
    protected $fillable = [
        'name',
        'path',
        'sort',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
