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

            $listing->addMediaFromUrl('https://loremflickr.com/640/480/business?random=' . rand(1, 1000))
                ->toMediaCollection('images');

            $listing->addMediaFromUrl('https://loremflickr.com/640/480/city?random=' . rand(1, 1000))
                ->toMediaCollection('images');

            try {
                $listing->addMediaFromUrl('https://raw.githubusercontent.com/intel-iot-devkit/sample-videos/master/classroom.mp4')
                    ->toMediaCollection('videos');
            } catch (\Exception $e) {
            }

            try {
                $listing->addMediaFromUrl('https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf')
                    ->withCustomProperties(['name' => 'Business Plan'])
                    ->toMediaCollection('documents');

                $listing->addMediaFromUrl('https://www.africau.edu/images/default/sample.pdf')
                    ->withCustomProperties(['name' => 'Financial Report'])
                    ->toMediaCollection('documents');
            } catch (\Exception $e) {
            }
        });
    }
}
