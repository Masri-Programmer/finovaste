<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\DonationListing;

class TransactionFeeCalculator
{
    /**
     * Calculate the fee for a given listing and amount.
     *
     * @param Listing $listing
     * @param float $amount
     * @return float
     */
    public function calculate(Listing $listing, float $amount): float
    {
        $percentage = config('fees.default');

        if ($listing->type === 'private' || $listing->type === 'private_occasion') {
            $percentage = config('fees.private');
        } elseif ($listing->listable_type === DonationListing::class) {
            $percentage = config('fees.charity');
        }

        // Calculate fee
        $fee = ($amount * $percentage) / 100;

        return round($fee, 2);
    }
}
