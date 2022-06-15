<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminRole>
 */
class AdminRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'guard_name' => 'sanctum',
            'name' => $this->faker->title,
            'description' => $this->faker->text(50),
            'sort' => $this->faker->numberBetween(10000, 9999),
            'status' => true,
        ];
    }
}
