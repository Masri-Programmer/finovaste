<?php

namespace App\Observers;

use App\Models\InvestmentListing;
use App\Mail\ListingUpdated;
use Illuminate\Support\Facades\Mail;

class InvestmentListingObserver
{
    public function updated(InvestmentListing $item): void
    {
        $listing = $item->listing;
        if (!$listing) return;

        $key = null;
        $params = [];

        if ($item->wasChanged('share_price')) {
            $key = 'updates.share_price_updated';
            $params = ['price' => number_format($item->share_price, 2) . "€"];
        } elseif ($item->wasChanged('investment_goal')) {
            $key = 'updates.investment_goal_updated';
            $params = ['goal' => number_format($item->investment_goal, 2) . "€"];
        }
        
        if ($key) {
             foreach ($listing->subscriptions as $subscriber) {
                $user = \App\Models\User::where('email', $subscriber->email)->first();
                $locale = $user ? $user->locale : config('app.locale');

                Mail::to($subscriber->email)->queue(new ListingUpdated($listing, [
                    'type' => 'update',
                    'key' => $key,
                    'params' => $params,
                    'subject_key' => 'updates.listing_updated_subject',
                    'url' => route('listings.show', $listing)
                ], $locale));
            }
        }
    }
}
