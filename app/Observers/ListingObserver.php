<?php

namespace App\Observers;

use App\Models\Listing;
use App\Mail\ListingUpdated;
use Illuminate\Support\Facades\Mail;

class ListingObserver
{
    public function updated(Listing $listing): void
    {
        $changes = [];
        
        if ($listing->wasChanged('title')) {
            $changes[] = 'Title updated';
        }
        if ($listing->wasChanged('description')) {
            $changes[] = 'Description updated';
        }
        if ($listing->wasChanged('status')) {
            $changes[] = "Status changed to {$listing->status}";
            
            // Log status change as before
            $listing->updates()->create([
                'title' => 'Status Update',
                'content' => "The listing status has changed to: {$listing->status}",
                'type' => 'status_change',
            ]);
        }
        if ($listing->wasChanged('meta')) {
            $changes[] = 'Details updated';
        }

        if (!empty($changes)) {
            // New logic: construct a key/param structure
            // For simplicity in this key-based refactor, we might need dynamic keys or a general one.
            // Let's use a general 'updates.fields_changed' key and pass the list of fields as a string for now, 
            // OR fully localize the field names.
            
            // Better approach:
            $fieldKeys = [];
             if ($listing->wasChanged('title')) $fieldKeys[] = __('attributes.title'); 
             if ($listing->wasChanged('description')) $fieldKeys[] = __('attributes.description');
             if ($listing->wasChanged('status')) $fieldKeys[] = __('attributes.status');
             if ($listing->wasChanged('meta')) $fieldKeys[] = __('attributes.details');
            
            // Note: usage of __() here uses the current app locale (the editor's locale). 
            // We want to send the keys to be translated in the recipient's locale.
            // So we should pass the RAW field names (e.g. 'title') and let the email view translate them if possible,
            // or we accept that "Title updated" is a constructed string. 
            
            // However, to strictly follow "translate to user locale", we should pass keys.
            // But combining multiple field updates into one message is tricky with simple keys.
            // Compromise: Use a generic "Listing Details Updated" key, or separate emails (too spammy).
            // Let's use a generic key `updates.general_update` and maybe pass changed fields as a param if needed, 
            // but `implode` of keys is hard to localize per user.
            
            // For now, let's assuming a generic "The listing has been updated." is sufficient for field edits, 
            // OR we rely on a complex key. Let's use 'updates.fields_updated' and pass the count or just a generic notification.
            
            foreach ($listing->subscriptions as $subscriber) {
                // Determine locale
                 // We need to check if 'subscriber' is a User model or just recorded email? 
                 // ListingSubscription has 'email', maybe 'locale' column if we added it? 
                 // If not, we can't fully know their locale unless they are a registered user.
                 // let's try to find a user by email.
                 $user = \App\Models\User::where('email', $subscriber->email)->first();
                 $locale = $user ? $user->locale : config('app.locale');

                Mail::to($subscriber->email)->queue(new ListingUpdated($listing, [
                    'type' => 'update',
                    'key' => 'updates.listing_general_update', // e.g. "The listing details have been updated."
                    'params' => [],
                    'subject_key' => 'updates.listing_updated_subject',
                    'url' => route('listings.show', $listing)
                ], $locale));
            }
        }
    }
}