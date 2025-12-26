<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Transaction;
use App\Mail\ListingUpdated;
use App\Traits\HasAppMessages; //
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    use HasAppMessages; //

    public function placeBid(Request $request, Listing $listing)
    {
        $request->validate(['amount' => 'required|numeric']);

        $auction = $listing->listable;

        if (!$auction || !method_exists($auction, 'bids')) {
            abort(404, 'Auction not found');
        }

        return DB::transaction(function () use ($request, $listing, $auction) {
            $auction = $auction->lockForUpdate()->find($auction->id);

            if ($request->amount <= $auction->current_bid) {
                // Keep ValidationException here as it maps to specific input fields perfectly
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'Bid must be higher than ' . number_format($auction->current_bid, 2)
                ]);
            }

            // Create the Bid Record
            $listing->bids()->create([
                'user_id' => Auth::id(),
                'amount' => $request->amount
            ]);

            // Update the cache column on the auction
            $auction->update(['current_bid' => $request->amount]);

            // Notify Subscribers
            foreach ($listing->subscriptions as $subscriber) {
                $user = \App\Models\User::where('email', $subscriber->email)->first();
                $locale = $user ? $user->locale : config('app.locale');

                 Mail::to($subscriber->email)->queue(new ListingUpdated($listing, [
                    'type' => 'bid',
                    'key' => 'updates.new_bid_placed',
                    'params' => ['amount' => number_format($request->amount, 2) . "€"],
                    'subject_key' => 'updates.new_bid_subject',
                    'url' => route('listings.show', $listing)
                ], $locale));
            }

             return $this->checkSuccess($listing, 'bid');
        });
    }

    public function buyItem(Request $request, Listing $listing, \App\Services\TransactionFeeCalculator $feeCalculator)
    {
        $item = $listing->listable;
        $quantity = $request->input('quantity', 1);

        if (!$item instanceof \App\Models\PurchaseListing) {
            abort(404);
        }

        return DB::transaction(function () use ($listing, $item, $quantity, $feeCalculator) {
            $item = $item->lockForUpdate()->find($item->id);

            // 2. Check Stock
            if ($item->quantity < $quantity) {
                // Switched to checkError for a standardized toast notification
                return $this->checkError('Not enough stock available');
            }

            $totalAmount = $item->price * $quantity;

            $transaction = Transaction::create([
                'uuid'         => Str::uuid(),
                'user_id'      => Auth::id(),
                'payable_type' => get_class($item),
                'payable_id'   => $item->id,
                'type'         => 'purchase',
                'amount'       => $totalAmount,
                'fee'          => $feeCalculator->calculate($listing, (float) $totalAmount),
                'currency'     => \App\Services\MoneyService::getCurrency(Auth::user()),
                'quantity'     => $quantity,
                'status'       => 'completed',
                'metadata'     => [
                    'snapshot_price' => $item->price,
                    'product_name'   => $listing->title,
                    'condition'      => $item->condition
                ]
            ]);

            // 4. Decrement Stock
            $item->decrement('quantity', $quantity);

             // Notify Subscribers
            foreach ($listing->subscriptions as $subscriber) {
                $user = \App\Models\User::where('email', $subscriber->email)->first();
                $locale = $user ? $user->locale : config('app.locale');

                 Mail::to($subscriber->email)->queue(new ListingUpdated($listing, [
                    'type' => 'buy',
                    'key' => 'updates.item_purchased',
                    'params' => ['stock' => ($item->quantity - $quantity)],
                    'subject_key' => 'updates.item_purchased_subject',
                    'url' => route('listings.show', $listing)
                ], $locale));
            }

            // NOTE: We cannot use $this->checkSuccess() here because it does not support
            // passing parameters to the route (we need the UUID for the receipt).
            // We manually construct the "notification" format to match the Trait's output.
            return redirect()->route('transactions.receipt', $transaction->uuid)
                ->with('notification', [
                    'type' => 'success',
                    'title' => __('messages.titles.success'),
                    'message' => 'Item purchased!',
                    'options' => [
                        'timeout' => 5000,
                        'closeOnClick' => true,
                        'icon' => true,
                    ]
                ]);
        });
    }

    // 3. Handle Donation
    public function donate(Request $request, Listing $listing, \App\Services\TransactionFeeCalculator $feeCalculator)
    {
        $donation = $listing->listable;

        if (!$donation instanceof \App\Models\DonationListing) {
            abort(404);
        }

        return DB::transaction(function () use ($request, $listing, $donation, $feeCalculator) {

            $donation = $donation->lockForUpdate()->find($donation->id);

            $transaction = Transaction::create([
                'uuid'         => Str::uuid(),
                'user_id'      => Auth::id(),
                'payable_type' => get_class($donation),
                'payable_id'   => $donation->id,
                'type'         => 'donation',
                'amount'       => $request->amount,
                'fee'          => $feeCalculator->calculate($listing, (float) $request->amount),
                'currency'     => \App\Services\MoneyService::getCurrency(Auth::user()),
                'status'       => 'completed',
                'metadata'     => [
                    'campaign_title' => $listing->title,
                    'donor_note'     => $request->input('note')
                ]
            ]);

            $donation->increment('amount_raised', $request->amount);
            $donation->increment('donors_count');

            // Notify Subscribers
            foreach ($listing->subscriptions as $subscriber) {
                $user = \App\Models\User::where('email', $subscriber->email)->first();
                $locale = $user ? $user->locale : config('app.locale');

                 Mail::to($subscriber->email)->queue(new ListingUpdated($listing, [
                    'type' => 'donation',
                    'key' => 'updates.new_donation_received',
                    'params' => ['amount' => number_format($request->amount, 2) . "€"],
                    'subject_key' => 'updates.new_donation_subject',
                    'url' => route('listings.show', $listing)
                ], $locale));
            }

            // Refactored to use checkSuccess
            return $this->checkSuccess($donation, 'donated');
        });
    }


    public function index(Request $request)
    {
        $filters = $request->validate([
            'type' => 'nullable|string|in:auction,donation',
            'search' => 'nullable|string',
        ]);

        $transactions = Transaction::where('user_id', Auth::id())
            ->with(['payable.listing'])
            ->when($filters['type'] ?? null, function ($q, $type) {
                $q->where('type', $type);
            })
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where('transaction_ref', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'filters' => $filters,
        ]);
    }

    public function receipt(Transaction $transaction)
    {
       Log::info('Receipt download requested', ['uuid' => $transaction->uuid, 'user_id' =>Auth::id()]);

        if ($transaction->user_id !==Auth::id()) {
           Log::warning('Unauthorized receipt access attempt', ['uuid' => $transaction->uuid, 'request_user' =>Auth::id(), 'owner_user' => $transaction->user_id]);
            abort(403);
        }

        if ($transaction->status !== 'completed') {
           Log::warning('Receipt requested for non-completed transaction', ['uuid' => $transaction->uuid, 'status' => $transaction->status]);
            return $this->checkError('Receipt is only available for completed transactions.');
        }

        try {
            $transaction->load(['payable.listing', 'user']);
           Log::info('Transaction loaded for receipt', ['uuid' => $transaction->uuid]);

            $pdf = Pdf::loadView('pdf.receipt', [
                'transaction' => $transaction,
            ]);

           Log::info('PDF view loaded', ['uuid' => $transaction->uuid]);

            return $pdf->download("receipt-{$transaction->uuid}.pdf");
        } catch (\Exception $e) {
           Log::error('PDF generation failed', [
                'uuid' => $transaction->uuid,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}