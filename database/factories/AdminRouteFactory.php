<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminRoute>
 */
class AdminRouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'controller' => $this->faker->word,
            'controller_name' => $this->faker->text(20),
            'action' => $this->faker->word,
            'action_name' => $this->faker->text(20),
            'public' => false,
        ];
    }
}
