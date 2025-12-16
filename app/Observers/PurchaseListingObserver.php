<?php

namespace App\Observers;

use App\Models\PurchaseListing;
use App\Mail\ListingUpdated;
use Illuminate\Support\Facades\Mail;

class PurchaseListingObserver
{
    public function updated(PurchaseListing $item): void
    {
        $listing = $item->listing;
        if (!$listing) return;

        $key = null;
        $params = [];

        if ($item->wasChanged('price')) {
            $key = 'updates.price_updated'; // "Price updated to :price"
            $params = ['price' => number_format($item->price, 2)];
        }elseif ($item->wasChanged('quantity') && $item->quantity > $item->getOriginal('quantity')) {
             $key = 'updates.stock_updated'; // "Stock increased to :quantity"
             $params = ['quantity' => $item->quantity];
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
