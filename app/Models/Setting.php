<?php

namespace App\Models;

use App\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property SettingGroup $group
 * @property SettingOption $option
 */
class Setting extends BaseModel
{
    protected $fillable = [
        'group_id',
        'option_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class, 'group_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(SettingOption::class, 'option_id');
    }
}
