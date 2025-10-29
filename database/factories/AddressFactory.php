<?php

namespace Database\Factories;

use Database\Factories\fake;
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
            'street' => $this->fake()->streetAddress(),
            'city' => 'Oldenburg',
            'state' => 'Lower Saxony',
            'zip' => $this->fake()->postcode(),
            'country' => 'Germany',
            'latitude' => $this->fake()->latitude(53.05, 53.25),
            'longitude' => $this->fake()->longitude(8.1, 8.3),
        ];
    }
}
