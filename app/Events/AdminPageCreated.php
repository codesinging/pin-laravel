<?php

namespace App\Events;

use App\Models\AdminPage;

class AdminPageCreated
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AdminPage $adminPage)
    {
        $adminPage->permission()->create([
            'name' => sprintf('%s:%s', $adminPage::class, $adminPage['id']),
            'guard_name' => $adminPage->guard_name,
        ]);
    }
}
