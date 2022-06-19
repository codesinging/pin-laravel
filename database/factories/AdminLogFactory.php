<?php

namespace Database\Factories;

use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminLog>
 */
class AdminLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => AdminUser::instance()->inRandomOrder()->value('id'),
            'method' => $this->faker->randomElement(['GET','POST', 'PUT', 'DELETE']),
            'path' => $this->faker->filePath(),
            'ip' => $this->faker->ipv4(),
            'status' => $this->faker->numberBetween(100, 599),
            'code' => $this->faker->numberBetween(0, 900000),
            'message' => $this->faker->text(20),
        ];
    }
}
