<?php

namespace Database\Factories;

use App\Models\Motorcycle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Motorcycle>
 */
class MotorcycleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array
    {
        return [
            'make' => $this->faker->randomElement(['Honda', 'Yamaha', 'BMW', 'Harley-Davidson', 'Suzuki']),
            'model' => $this->faker->word(),
            'year' => $this->faker->numberBetween(2018, 2024),
            'price_per_day' => $this->faker->randomFloat(2, 50, 500),
            'status' => $this->faker->randomElement(['available', 'rented', 'maintenance']),
            'image_url' => $this->faker->imageUrl(300, 200, 'motorcycle'),
            'description' => $this->faker->sentence(),
        ];
    }
}
