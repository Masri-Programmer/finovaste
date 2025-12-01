<?php

namespace App\Observers;

use App\Models\ListingUpdate;

class ListingUpdateObserver
{
    /**
     * Handle the ListingUpdate "created" event.
     */
   public function created(ListingUpdate $update): void
{
    $listing = $update->listing;
    
    // Process in chunks to handle memory if many subscribers
    $listing->subscriptions()->chunk(100, function ($subscribers) use ($listing, $update) {
        foreach ($subscribers as $subscriber) {
            \Illuminate\Support\Facades\Mail::to($subscriber->email)
                ->locale($subscriber->locale) // <--- MAGIC METHOD: Sets language for this specific email
                ->queue(new \App\Mail\ListingUpdated($listing, $update));
        }
    });
}
}