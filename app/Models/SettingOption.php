<?php

namespace App\Models;

use App\Enums\SettingTypes;
use App\Events\SettingOptionCreated;
use App\Events\SettingOptionDeleted;
use App\Events\SettingOptionUpdated;
use App\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property Setting $setting
 */
class SettingOption extends BaseModel
{
    protected $fillable = [
        'group_id',
        'name',
        'description',
        'key',
        'type',
        'value',
        'attributes',
        'data',
        'initial',
        'sort',
        'status',
    ];

    protected $casts = [
        'value' => 'array',
        'attributes' => 'array',
        'data' => 'array',
        'initial' => 'boolean',
        'status' => 'boolean',
    ];

    protected $appends = [
        'type_label',
    ];

    protected $with = [
        'group',
        'setting',
    ];

    protected $dispatchesEvents = [
        'created' => SettingOptionCreated::class,
        'updated' => SettingOptionUpdated::class,
        'deleted' => SettingOptionDeleted::class,
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class, 'group_id');
    }

    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class, 'option_id');
    }

    public function typeLabel(): Attribute
    {
        return new Attribute(get: fn($value, $attributes) => SettingTypes::of($attributes['type'])?->value);
    }
}
