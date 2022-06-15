<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests;

use App\Models\Administrator;

trait ActingAsAdministrator
{
    protected function actingAsAdministrator(array $attributes = []): static
    {
        $admin = Administrator::factory()->create($attributes);
        $this->actingAs($admin);
        return $this;
    }

    protected function actingAsSuperAdministrator(array $attributes = []): static
    {
        $admin = Administrator::factory()->create(array_merge($attributes, ['super' => true]));
        $this->actingAs($admin);
        return $this;
    }
}
