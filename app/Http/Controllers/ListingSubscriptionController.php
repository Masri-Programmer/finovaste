<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingSubscription;
use Illuminate\Http\Request;

class ListingSubscriptionController extends Controller
{
public function store(Request $request, Listing $listing)
{
    $request->validate(['email' => 'required|email']);

    // Determine locale: if logged in, use user pref, otherwise use current app locale
    $locale = $request->user()?->locale ?? app()->getLocale();

    ListingSubscription::firstOrCreate(
        ['listing_id' => $listing->id, 'email' => $request->email],
        ['locale' => $locale] // Only sets this on creation
    );

    return $this->checkSuccess(ListingSubscription::class,);
}
}