<?php

namespace Database\Factories;

use App\Models\DonationListing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DonationListing>
 */
class DonationListingFactory extends Factory
{
    protected $model = DonationListing::class;

    public function definition(): array
    {
        $goal = $this->faker->numberBetween(5000, 100000);
        $raised = $this->faker->numberBetween(0, $goal);

        return [
            'donation_goal' => $goal,
            'amount_raised' => $raised,
            'donors_count' => $this->faker->numberBetween(0, 100),
            'is_goal_flexible' => $this->faker->boolean(70),
        ];
    }

    public function withListing()
    {
        return $this->afterCreating(function (DonationListing $donation) {
            $donation->listing()->save(
                \App\Models\Listing::factory()->make()
            );
        });
    }
}
