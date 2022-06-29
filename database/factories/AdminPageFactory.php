<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminPage>
 */
class AdminPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(10),
            'path' => $this->faker->unique()->filePath(),
            'sort' => $this->faker->numberBetween(100000, 999999),
            'public' => false,
            'status' => true,
        ];
    }
}
