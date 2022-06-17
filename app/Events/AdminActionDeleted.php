<?php

namespace App\Events;


use App\Models\AdminAction;

class AdminActionDeleted
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AdminAction $adminAction)
    {
        $adminAction->permission()->delete();
    }
}
