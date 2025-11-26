<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingFaqFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'listing_id' => Listing::factory(),
            
            'question' => [
                'en' => rtrim($this->faker->sentence(rand(5, 10)), '.') . '?',
                'de' => 'Ist das eine Testfrage für diesen Artikel? ' . $this->faker->word . '?',
            ],
            
            'answer' => [
                'en' => $this->faker->paragraph(2),
                'de' => 'Dies ist eine automatische Antwort für Entwicklungszwecke. ' . $this->faker->sentence(),
            ],
            
            'is_visible' => true,
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }
}