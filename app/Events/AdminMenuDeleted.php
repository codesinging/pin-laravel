<?php

namespace App\Events;

use App\Models\AdminMenu;

class AdminMenuDeleted
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AdminMenu $adminMenu)
    {
        $adminMenu->permission()->delete();
    }
}
