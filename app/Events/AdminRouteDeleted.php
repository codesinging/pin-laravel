<?php

namespace App\Events;


use App\Models\AdminRoute;

class AdminRouteDeleted
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AdminRoute $adminRoute)
    {
        $adminRoute->permission()->delete();
    }
}
