<?php

namespace App\Observers;

use App\Models\Listing;
use App\Mail\ListingUpdated;
use Illuminate\Support\Facades\Mail;

class ListingObserver
{
    public function updated(Listing $listing): void
    {
        if ($listing->wasChanged(['title', 'description', 'status', 'meta'])) {
            
            $subscribers = $listing->subscriptions;

            foreach ($subscribers as $subscriber) {
                // Queue the email
                Mail::to($subscriber->email)->queue(new ListingUpdated($listing));
            }
        }
        if ($listing->wasChanged('status')) {
        $listing->updates()->create([
            'title' => 'Status Update',
            'content' => "The listing status has changed to: {$listing->status}",
            'type' => 'status_change',
        ]);
    }
        
        // Also check if the polymorphic relation (price, auction details) changed
        // This requires slightly more complex logic depending on how you save the relation,
        // but typically handled in the Controller transaction. 
        // For simplicity, you can trigger this manually in the Update method of ListingController if preferred.
    }
}