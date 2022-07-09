<?php

namespace App\Models;

use App\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminLogin extends BaseModel
{
    protected $fillable = [
        'user_id',
        'time',
        'ip',
        'result',
        'code',
        'message',
    ];

    protected $casts = [
        'time' => 'datetime',
    ];

    protected $with = [
        'user',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'user_id');
    }
}
