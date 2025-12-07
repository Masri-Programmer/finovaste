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
             // Prepare data once
             $updateData = [
                'type' => $update->type,
                'title' => $update->title,
                'message' => $update->content,
                'subject_key' => ($update->type === 'manual' ? 'updates.email_subject_manual' : 'updates.listing_updated_subject'),
                 // We can pass params if we had them, but ListingUpdate stores raw string usually.
                'key' => 'updates.listing_general_update', 
                'params' => [], 
             ];

             // If type is manual, we use the message directly.
             // If manual, we set message in 'message'. My blade template uses message if Key is missing or fallback?
             // Step 108: if type == manual, it shows title/message. OK.

            foreach ($subscribers as $subscriber) {
                // Determine locale
                $locale = $subscriber->locale ?? config('app.locale'); 
                // Note: Subscriber might be a user model if relation is defined, or just ListingSubscription model.
                // ListingSubscription model (Step 99) only has email. It doesn't seem to have 'locale' column unless I missed it.
                // But ListingUpdateObserver (Step 196) has: ->locale($subscriber->locale).
                // Existing code assumes $subscriber has locale. Maybe ListingSubscription DOES have it?
                // Step 24 file list showed ListingSubscription.php size 398 bytes. Step 99 showed it. It has 'email', 'listing_id'. 
                // Step 7 ListingSubscriptionController stores 'locale'.
                // So yes, it has locale.

                \Illuminate\Support\Facades\Mail::to($subscriber->email)
                    ->queue(new \App\Mail\ListingUpdated($listing, $updateData, $locale));
            }
        });
    }
}