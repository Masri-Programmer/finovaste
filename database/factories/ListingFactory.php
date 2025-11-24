<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    protected $model = Listing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $category = Category::inRandomOrder()->first() ?? Category::factory()->create();
        $address = $user->addresses()->inRandomOrder()->first();

        return [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address?->id,
            'status' => $this->faker->randomElement(['active', 'pending', 'expired']),
            'is_featured' => $this->faker->boolean(10), // 10% chance of being featured
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),

            // Translatable fields
            'title' => [
                'en' => rtrim($this->faker->sentence(4, true), '.'),
                'de' => $this->faker->realText(50),
            ],
            'description' => [
                'en' => $this->faker->realText(300),
                'de' => $this->faker->realText(300),
            ],
        ];
    }
}
