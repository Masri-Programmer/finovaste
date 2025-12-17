<?php

namespace Database\Factories;

use App\Models\AuctionListing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuctionListing>
 */
class AuctionListingFactory extends Factory
{
    protected $model = AuctionListing::class;

    public function definition(): array
    {
        $startPrice = $this->faker->numberBetween(1000, 50000);

        return [
            'start_price' => $startPrice,
            'reserve_price' => $startPrice * 1.5,
            'purchase_price' => $startPrice * 2,
            'current_bid' => $this->faker->numberBetween($startPrice, $startPrice * 1.5),
            'starts_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'ends_at' => $this->faker->dateTimeBetween('now', '+2 weeks'),
        ];
    }

    public function withListing()
    {
        return $this->afterCreating(function (AuctionListing $auction) {
            $auction->listing()->save(
                \App\Models\Listing::factory()->make()
            );
        });
    }
}
