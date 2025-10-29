<?php

namespace Database\Factories;

// Remove the incorrect 'use Database\Factories\fake;' line.
// Faker is accessed via the $this->faker property provided by the base Factory class.
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Change $this->fake() to $this->faker
            'street' => $this->faker->streetAddress(),
            'city' => 'Oldenburg',
            'state' => 'Lower Saxony',
            'zip' => $this->faker->postcode(),
            'country' => 'Germany',
            'latitude' => $this->faker->latitude(53.05, 53.25),
            'longitude' => $this->faker->longitude(8.1, 8.3),
        ];
    }
}
