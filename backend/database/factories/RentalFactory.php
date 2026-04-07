<?php

namespace Database\Factories;

use App\Models\Rental;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'motorcycle_id' => \App\Models\Motorcycle::factory(),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d'),
            'total_price' => $this->faker->randomFloat(2, 200, 5000),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
        ];
    }
}
