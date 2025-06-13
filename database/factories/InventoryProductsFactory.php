<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryProducts>
 */
class InventoryProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'qty' => fake()->numberBetween(1, 100),
            'should_alert' => fake()->boolean(),
            'alert_threshold' => fake()->numberBetween(1, 50),
        ];
    }
}
