<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Bid;
use App\Models\AuctionListing; // Assuming this is your auction model
use App\Services\ListingUpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    public function store(Request $request, Listing $listing)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // 1. Validation: Basic Checks
        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in.');
        }

        if ($listing->user_id === Auth::id()) {
            return back()->with('error', 'You cannot bid on your own listing.');
        }

        // Ensure this listing is actually an auction
        if ($listing->listable_type !== 'App\Models\AuctionListing') { // Or however you map your morph map
            return back()->with('error', 'This listing is not an auction.');
        }

        // 2. THE TRANSACTION: Critical for Data Integrity
        try {
            DB::transaction(function () use ($request, $listing) {

                // A. Lock the rows. This prevents anyone else from writing to these
                // specific rows until this transaction finishes.
                $auctionData = $listing->listable()->lockForUpdate()->first();

                // B. Timing Validation
                $now = now();
                if ($now < $auctionData->starts_at) {
                    throw new \Exception("The auction has not started yet.");
                }
                if ($now > $auctionData->ends_at) {
                    throw new \Exception("The auction has ended.");
                }

                // C. Price Logic
                $incomingBid = $request->amount;
                // $currentHigh = $auctionData->current_bid ?? $auctionData->start_price;

                // If there are NO bids yet, the bid must be >= start_price
                // If there ARE bids, the bid must be > current_bid
                // (You can add 'min_increment' logic here later)

                // Logic: Strict check that new bid is higher than current high
                // Note: If it's the very first bid, current_bid might be null, 
                // so we compare against start_price.
                $minRequired = ($auctionData->current_bid)
                    ? $auctionData->current_bid + 0.01 // +1 cent increment minimum
                    : $auctionData->start_price;

                if ($incomingBid < $minRequired) {
                    throw new \Exception("Your bid must be at least " . number_format($minRequired, 2));
                }

                // D. Create the Bid
                $bid = Bid::create([
                    'user_id'    => Auth::id(),
                    'listing_id' => $listing->id,
                    'amount'     => $incomingBid,
                    'status'     => 'active',
                    'ip_address' => $request->ip(),
                ]);

                // E. Update the Auction Cache
                // We update the 'current_bid' column on the child table for fast read access later
                $auctionData->update([
                    'current_bid' => $incomingBid
                ]);

                // Optional: Sniping Protection
                // If bid is placed in last 5 minutes, extend auction by 5 minutes
                // if ($auctionData->ends_at->diffInMinutes($now) < 5) {
                //     $auctionData->update(['ends_at' => $auctionData->ends_at->addMinutes(5)]);
                // }

                 // Updates for system
                ListingUpdateService::system($listing, 'updates.bid_new', [
                    'amount' => number_format($incomingBid, 2) . '€'
                ]);
            });

            return back()->with('success', 'Bid placed successfully!');

        } catch (\Exception $e) {
            // If anything in the transaction fails, it rolls back automatically
            return back()->with('error', $e->getMessage());
        }
    }
}
