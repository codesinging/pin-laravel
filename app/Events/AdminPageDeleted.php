<?php

namespace App\Events;

use App\Models\AdminPage;

class AdminPageDeleted
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AdminPage $adminPage)
    {
        $adminPage->permission()->delete();
    }
}
