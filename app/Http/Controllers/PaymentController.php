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
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    public function checkout(Request $request, Listing $listing)
    {
        $user = Auth::user();

        // 1. Ownership Check
        if ($listing->user_id === $user->id) {
            // [3] Use checkError
            return $this->checkError('You cannot buy your own listing.');
        }

        $listing->load('listable');

        $amount = 0;
        $productName = $listing->title;
        $transactionType = 'purchase';
        $quantity = 1;
        $metadata = [];

        // 2. Calculate Amount & Validation based on Type
        switch ($listing->listable_type) {
            case BuyNowListing::class:
                $amount = $listing->listable->price;
                if ($listing->listable->quantity < 1) {
                    return $this->checkError('Item out of stock.');
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
                $quantity = $request->shares;
                $transactionType = 'investment';
                $productName = "Investment: {$request->shares} shares in {$listing->title}";

                // CRITICAL FIX: Stripe metadata values must be strings
                $metadata['shares_count'] = (string) $request->shares;

                $remaining = $listing->listable->shares_offered - $listing->listable->investors_count;
                if ($quantity > $remaining) {
                     return $this->checkError('Not enough shares available.');
                }
                break;

            case AuctionListing::class:
                 if (!$listing->listable->buy_it_now_price) {
                     return $this->checkError('Buy Now not available for this auction.');
                 }
                 $amount = $listing->listable->buy_it_now_price;
                 $transactionType = 'auction_purchase';
                 break;

            default:
                return $this->checkError('Invalid listing type.');
        }

        // 3. Create Pending Transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'payable_type' => $listing->listable_type,
            'payable_id' => $listing->listable_id,
            'amount' => $amount,
            'currency' => 'USD',
            'status' => 'pending',
            'type' => $transactionType,
            'metadata' => $metadata,
            'transaction_ref' => 'temp_' . uniqid(),
        ]);

        // 4. Initialize Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Prepare image for Stripe (ensure it's a valid URL)
        $imageUrl = $listing->getFirstMediaUrl('images', 'thumb');
        $images = $imageUrl ? [$imageUrl] : [];

        try {
            $checkoutSession = Session::create([
                // Option A: Explicitly defined
                'payment_method_types' => ['card', 'paypal'],

                // Option B: Recommended
                // 'automatic_payment_methods' => ['enabled' => true],

                'customer_email' => $user->email,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $productName,
                            'images' => $images,
                        ],
                        'unit_amount' => (int)($amount * 100), // Cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('listings.success', $listing->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('listings.show', $listing->id),
                'client_reference_id' => (string) $transaction->id,
                'metadata' => array_merge([
                    'transaction_uuid' => (string) $transaction->uuid,
                    'listing_id' => (string) $listing->id,
                ], $metadata),
            ]);
        } catch (\Exception $e) {
            // [4] Pass exception to checkError for automatic logging if debug is on
            return $this->checkError('Payment initialization failed: ' . $e->getMessage(), $e);
        }

        // 5. Update Transaction with real Stripe Session ID
        $transaction->update(['transaction_ref' => $checkoutSession->id]);

        return Inertia::location($checkoutSession->url);
    }

    public function success(Request $request, Listing $listing)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            abort(404);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = Session::retrieve($sessionId);
        } catch (\Exception $e) {
             // [5] Use checkError (Redirects back)
             return $this->checkError('Invalid Session.', $e);
        }

        // Note: For PayPal, status might briefly be 'processing', but usually 'paid'
        if ($session->payment_status !== 'paid') {
           return $this->checkError('Payment not completed or is processing.');
        }

        $transaction = Transaction::where('transaction_ref', $sessionId)->firstOrFail();

        if ($transaction->status !== 'completed') {
            DB::transaction(function () use ($transaction, $listing, $session) {
                // 1. Mark Paid
                $transaction->update([
                    'status' => 'completed',
                    'payment_method' => $session->payment_method_types[0] ?? 'stripe', // Capture method
                ]);

                // 2. Handle Stock / Updates based on Type
                $item = $listing->listable;

                if ($item instanceof BuyNowListing) {
                    $item->decrement('quantity', 1);
                }

                // Add Investment logic if needed
                if ($item instanceof InvestmentListing) {
                    // $item->increment('investors_count', $transaction->metadata['shares_count']);
                }
            });
        }

        return Inertia::render('Payment/Success', [
            'listing' => $listing,
            'transactionId' => $transaction->uuid,
        ]);
    }
}