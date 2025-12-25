<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingFactory extends Factory
{
    protected $model = Listing::class;

    public function definition(): array
    {
        // Optimization: Use factory helpers instead of DB queries for speed
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            // We leave address null by default to avoid creating too many addresses, 
            // or pick one if the user exists.
            'address_id' => null, 
            
            'status' => $this->faker->randomElement(['active', 'pending', 'expired']),
            'is_featured' => $this->faker->boolean(10),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            
            // Note: 'expires_at' should usually match the auction end time.
            // We set a default here, but the AuctionFactory will override it.
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),

            // JSON Translatable fields
            'title' => [
                'en' => rtrim($this->faker->sentence(4, true), '.'),
                'de' => $this->faker->realText(50),
            ],
            'description' => [
                'en' => $this->faker->realText(300),
                'de' => $this->faker->realText(300),
            ],
            'type' => 'standard', // Default, overridden by children
        ];
    }
}