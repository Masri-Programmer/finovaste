<?php

namespace Database\Factories;

use App\Models\InvestmentListing;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvestmentListing>
 */
class InvestmentListingFactory extends Factory
{
    protected $model = InvestmentListing::class;

    public function definition(): array
    {
        $goal = $this->faker->numberBetween(50000, 1000000);
        $shares = $this->faker->numberBetween(500, 5000);
        $sharePrice = ceil($goal / $shares); // Calculate share price based on goal/shares
        $raised = $this->faker->numberBetween(0, $goal);

        return [
            'investment_goal' => $this->faker->numberBetween(50000, 1000000),
            'minimum_investment' => 1500,
            'shares_offered' => 1000,
            'share_price' => 150,
            'amount_raised' => 0,
            'investors_count' => 0,
        ];
    }

    /**
     * Create the parent Listing model for this specific listing.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withListing()
    {
        return $this->afterCreating(function ($model) {
            $listing = Listing::factory()->make();
            $model->listing()->save($listing);
        });
    }
}
