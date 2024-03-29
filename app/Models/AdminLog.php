<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Models;

use App\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property AdminUser $user
 */
class AdminLog extends BaseModel
{
    protected $fillable = [
        'user_id',
        'route_id',
        'method',
        'path',
        'ip',
        'input',
        'status',
        'code',
        'message',
    ];

    protected $casts = [
        'input' => 'array',
    ];

    protected $with = [
        'user',
        'route',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'user_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(AdminRoute::class, 'route_id');
    }
}
