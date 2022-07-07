<?php

namespace App\Events;

use App\Models\AdminRoute;

class AdminRouteCreated
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AdminRoute $adminRoute)
    {
        if (!$adminRoute->isPublic()){
            $adminRoute->permission()->create([
                'name' => sprintf('%s:%s', $adminRoute::class, $adminRoute['id']),
                'guard_name' => $adminRoute->guard_name,
            ]);
        }
    }
}
