<?php

namespace App\Events;

use App\Models\AdminRoute;

class AdminRouteUpdated
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AdminRoute $adminRoute)
    {
        if ($adminRoute->isPublic()){
            $adminRoute->permission()->delete();
        } else {
            $adminRoute->permission()->firstOrCreate([
                'name' => sprintf('%s:%s', $adminRoute::class, $adminRoute['id']),
                'guard_name' => $adminRoute->guard_name,
            ]);
        }
    }
}
