<?php

namespace Database\Factories;

use App\Models\InventoryProducts;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1, // Will be set in the seeder or test
            'activity_type' => $this->faker->randomElement(['Logged in', 'Logged out', 'Product updated', 'Product created', 'Product deleted']),
            'activity_data' => [],
            'activity_label' => $this->faker->sentence,
            'subject_id' => 4,
            'subject_type' => $this->faker->randomElement([InventoryProducts::class, User::class]),
        ];
    }
}
