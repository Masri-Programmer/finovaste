<?php

namespace App\Observers;

use App\Models\Review;
use App\Mail\ListingUpdated;
use Illuminate\Support\Facades\Mail;

class ReviewObserver
{
    public function created(Review $review): void
    {
        $listing = $review->listing;
        
        foreach ($listing->subscriptions as $subscriber) {
            if ($subscriber->email === $review->user->email) {
                continue;
            }

            $user = \App\Models\User::where('email', $subscriber->email)->first();
            $locale = $user ? $user->locale : config('app.locale');

            Mail::to($subscriber->email)->queue(new ListingUpdated($listing, [
                'type' => 'review',
                'key' => 'updates.new_review_body', // "A new review has been posted: :body"
                'params' => ['body' => Str::limit($review->body, 50)],
                'subject_key' => 'updates.new_review_subject',
                'url' => route('listings.show', $listing) . '#reviews'
            ], $locale));
        }
    }
}
