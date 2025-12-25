<?php

namespace App\Observers;

use App\Models\DonationListing;
use App\Mail\ListingUpdated;
use Illuminate\Support\Facades\Mail;

class DonationListingObserver
{
    public function updated(DonationListing $item): void
    {
        $listing = $item->listing;
        if (!$listing) return;

        if ($item->wasChanged('target')) {
             foreach ($listing->subscriptions as $subscriber) {
                $user = \App\Models\User::where('email', $subscriber->email)->first();
                $locale = $user ? $user->locale : config('app.locale');

                Mail::to($subscriber->email)->queue(new ListingUpdated($listing, [
                    'type' => 'update',
                    'key' => 'updates.target_updated', // "Goal updated to :goal"
                    'params' => ['goal' => number_format($item->target, 2) . "€"],
                    'subject_key' => 'updates.listing_updated_subject',
                    'url' => route('listings.show', $listing)
                ], $locale));
            }
        }
    }
}
