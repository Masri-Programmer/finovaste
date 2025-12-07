<?php

namespace App\Observers;

use App\Models\AuctionListing;
use App\Mail\ListingUpdated;
use Illuminate\Support\Facades\Mail;

class AuctionListingObserver
{
    public function updated(AuctionListing $item): void
    {
        $listing = $item->listing;
        if (!$listing) return;
        
        if ($item->wasChanged(['starting_bid', 'reserve_price', 'end_time'])) {
             
             foreach ($listing->subscriptions as $subscriber) {
                $user = \App\Models\User::where('email', $subscriber->email)->first();
                $locale = $user ? $user->locale : config('app.locale');

                Mail::to($subscriber->email)->queue(new ListingUpdated($listing, [
                    'type' => 'update',
                    'key' => 'updates.auction_details_updated', // "Auction details have been updated."
                    'params' => [],
                    'subject_key' => 'updates.listing_updated_subject', 
                    'url' => route('listings.show', $listing)
                ], $locale));
            }
        }
    }
}
