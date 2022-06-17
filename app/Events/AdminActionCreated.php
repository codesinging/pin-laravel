<?php

namespace App\Events;

use App\Models\AdminAction;

class AdminActionCreated
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AdminAction $adminAction)
    {
        $adminAction->permission()->create([
            'name' => sprintf('%s:%s', $adminAction::class, $adminAction['id']),
            'guard_name' => $adminAction->guard_name,
        ]);
    }
}
