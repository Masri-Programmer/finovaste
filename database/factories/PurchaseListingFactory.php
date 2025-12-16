<?php

namespace Database\Factories;

use App\Models\PurchaseListing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseListing>
 */
class PurchaseListingFactory extends Factory
{
    protected $model = PurchaseListing::class;

    public function definition(): array
    {
        return [
            'price' => $this->faker->numberBetween(100, 100000),
            'quantity' => $this->faker->numberBetween(1, 100),
            'condition' => $this->faker->randomElement(['new', 'used', 'refurbished']),
        ];
    }

    public function withListing()
    {
        return $this->afterCreating(function (PurchaseListing $purchase) {
            $purchase->listing()->save(
                \App\Models\Listing::factory()->make()
            );
        });
    }
}
