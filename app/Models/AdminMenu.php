<?php

namespace App\Models;

use App\Events\AdminMenuCreated;
use App\Events\AdminMenuDeleted;
use App\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property AdminPermission $permission
 * @property AdminPage $page
 */
class AdminMenu extends BaseModel
{
    use NodeTrait;

    public string $guard_name = 'sanctum';

    protected $fillable = [
        'page_id',
        'name',
        'icon',
        'sort',
        'public',
        'default',
        'opened',
        'status',
    ];

    protected $hidden = [
        '_lft',
        '_rgt',
    ];

    protected $casts = [
        'public' => 'boolean',
        'default' => 'boolean',
        'opened' => 'boolean',
        'status' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'created' => AdminMenuCreated::class,
        'deleted' => AdminMenuDeleted::class,
    ];

    protected $with = [
        'page',
    ];

    public function isPublic(): bool
    {
        return (bool)($this->attributes['public'] ?? false);
    }

    public function permission(): MorphOne
    {
        return $this->morphOne(AdminPermission::class, 'permissionable');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(AdminPage::class, 'page_id');
    }
}
