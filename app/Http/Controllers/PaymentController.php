<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Transaction;
use App\Models\BuyNowListing;
use App\Models\DonationListing;
use App\Models\InvestmentListing;
use App\Models\AuctionListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    public function checkout(Request $request, Listing $listing)
    {
        $user = Auth::user();
        $listing->load('listable'); // Load the specific sub-model

        $amount = 0;
        $productName = $listing->title;
        $transactionType = 'purchase';
        $quantity = 1;
        $metadata = [];

        // 1. Calculate Amount & Validation based on Type
        switch ($listing->listable_type) {
            case BuyNowListing::class:
                $amount = $listing->listable->price;
                if ($listing->listable->quantity < 1) {
                    return back()->with('error', 'Item out of stock.');
                }
                break;

            case DonationListing::class:
                $request->validate(['amount' => 'required|numeric|min:1']);
                $amount = $request->amount;
                $transactionType = 'donation';
                $productName = "Donation to: {$listing->title}";
                break;

            case InvestmentListing::class:
                $request->validate(['shares' => 'required|integer|min:1']);
                $amount = $listing->listable->share_price * $request->shares;
                $quantity = $request->shares; // Stored for logic, not Stripe qty
                $transactionType = 'investment';
                $productName = "Investment: {$request->shares} shares in {$listing->title}";
                $metadata['shares_count'] = $request->shares;
                
                // Check available shares
                $remaining = $listing->listable->shares_offered - $listing->listable->investors_count; // Simplified logic
                // Real logic might need to sum 'shares_sold' column if you have one
                break;
            
            case AuctionListing::class:
                 // Assuming "Buy It Now" for this flow
                 if (!$listing->listable->buy_it_now_price) {
                     return back()->with('error', 'Buy Now not available for this auction.');
                 }
                 $amount = $listing->listable->buy_it_now_price;
                 $transactionType = 'auction_buy_now';
                 break;

            default:
                return back()->with('error', 'Invalid listing type.');
        }

        // 2. Create Pending Transaction (So we have a record BEFORE they pay)
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'payable_type' => $listing->listable_type,
            'payable_id' => $listing->listable_id,
            'amount' => $amount,
            'currency' => 'USD',
            'status' => 'pending',
            'type' => $transactionType,
            'metadata' => $metadata,
            'transaction_ref' => 'temp_' . uniqid(), // Temporary, will update with Stripe ID
        ]);

        // 3. Initialize Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $user->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $productName,
                        'images' => [$listing->getFirstMediaUrl('images', 'thumb')], // Optional
                    ],
                    'unit_amount' => (int)($amount * 100), // Cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('listings.show', $listing->id),
            'client_reference_id' => $transaction->id, // Link Stripe to DB ID
            'metadata' => [
                'transaction_uuid' => $transaction->uuid,
                'listing_id' => $listing->id,
            ],
        ]);

        // 4. Update Transaction with real Stripe Session ID
        $transaction->update(['transaction_ref' => $checkoutSession->id]);

        return Inertia::location($checkoutSession->url);
    }
    
    public function success(Request $request)
    {
        // Simple success page, actual logic happens in Webhook
        return Inertia::render('Payment/Success');
    }
}