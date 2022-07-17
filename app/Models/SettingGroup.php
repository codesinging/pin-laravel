<?php

namespace App\Models;

use App\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property Setting[]|Collection $settings
 */
class SettingGroup extends BaseModel
{
    protected $fillable = [
        'name',
        'key',
        'description',
        'sort',
    ];

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class, 'group_id');
    }
}
