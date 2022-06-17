<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests;

use App\Models\AdminUser;

trait ActingAsAdminUser
{
    protected function actingAsAdminUser(array $attributes = []): static
    {
        $admin = AdminUser::factory()->create($attributes);
        $this->actingAs($admin);
        return $this;
    }

    protected function actingAsSuperAdminUser(array $attributes = []): static
    {
        $admin = AdminUser::factory()->create(array_merge($attributes, ['super' => true]));
        $this->actingAs($admin);
        return $this;
    }
}
