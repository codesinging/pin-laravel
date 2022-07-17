<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'group_id' => $this->faker->numberBetween(1, 100),
            'option_id' => $this->faker->numberBetween(1, 100),
            'key' => $this->faker->unique()->word(),
            'value' => $this->faker->text(10),
        ];
    }
}
