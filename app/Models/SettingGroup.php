<?php

namespace App\Models;

use App\Support\Model\BaseModel;

class SettingGroup extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'sort',
    ];
}
