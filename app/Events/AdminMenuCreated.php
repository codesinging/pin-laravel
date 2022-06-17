<?php

namespace App\Events;

use App\Models\AdminMenu;

class AdminMenuCreated
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AdminMenu $adminMenu)
    {
        $adminMenu->permission()->create([
            'name' => sprintf('%s:%s', $adminMenu::class, $adminMenu['id']),
            'guard_name' => $adminMenu->guard_name,
        ]);
    }
}
