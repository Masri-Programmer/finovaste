<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        try {
            if ($payload['type'] === 'checkout.session.completed') {
                $session = $payload['data']['object'];
                
                // Find the transaction by the ref we saved earlier
                $transaction = Transaction::where('transaction_ref', $session['id'])->first();

                if ($transaction && $transaction->status !== 'completed') {
                    
                    // 1. Mark Transaction as Paid
                    $transaction->update([
                        'status' => 'completed',
                        'payment_provider' => 'stripe',
                        // You can capture the fee here if you do a retrieve call to Stripe API
                    ]);

                    // 2. Update the Listing Stats (Polymorphic)
                    $payable = $transaction->payable; // The specific listing model

                    if ($payable) {
                        switch ($transaction->payable_type) {
                            case 'App\Models\DonationListing':
                                $payable->increment('amount_raised', $transaction->amount);
                                $payable->increment('donors_count');
                                break;
                            case 'App\Models\AuctionListing':
                                // Mark auction as ended/sold if this was a Buy It Now
                                $payable->update(['current_bid' => $transaction->amount]); // Or specific logic
                                break;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook Handled with error'], 500);
        }

        return response()->json(['status' => 'success']);
    }
}