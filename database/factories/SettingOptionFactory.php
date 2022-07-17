<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SettingOption>
 */
class SettingOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'group_id' => $this->faker->numberBetween(1, 99),
            'name' => $this->faker->word(),
            'description' => $this->faker->text(20),
            'key' => $this->faker->unique()->word(),
            'type' => 'Input',
            'value' => $this->faker->text(10),
            'sort' => $this->faker->numberBetween(1, 9999),
            'initial' => true,
            'status' => true,
        ];
    }
}
