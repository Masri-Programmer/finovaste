<?php

namespace Database\Factories;

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
