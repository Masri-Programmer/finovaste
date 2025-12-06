<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;


class TransactionController extends Controller
{
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
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'Bid must be higher than ' . number_format($auction->current_bid, 2)
                ]);
            }

            // Create the Bid Record
            // Note: This usually goes into a 'bids' table, not the 'transactions' table yet.
            // The 'transaction' happens only when the auction is WON and paid for.
            $listing->bids()->create([
                'user_id' => Auth::id(),
                'amount' => $request->amount
            ]);

            // Update the cache column on the auction
            $auction->update(['current_bid' => $request->amount]);

            return back()->with('success', 'Bid placed successfully!');
        });
    }

    public function buyItem(Request $request, Listing $listing)
    {
        $item = $listing->listable;
        $quantity = $request->input('quantity', 1);

        if (!$item instanceof \App\Models\BuyNowListing) {
            abort(404);
        }

        return DB::transaction(function () use ($listing, $item, $quantity) {
            $item = $item->lockForUpdate()->find($item->id);

            // 2. Check Stock
            if ($item->quantity < $quantity) {
                return back()->withErrors(['quantity' => 'Not enough stock available']);
            }

            $totalAmount = $item->price * $quantity;

            $transaction = Transaction::create([
                'uuid'         => Str::uuid(),
                'user_id'      => Auth::id(),

                // Polymorphic Link (The Magic)
                'payable_type' => get_class($item),
                'payable_id'   => $item->id,

                'type'         => 'buy_now',
                'amount'       => $totalAmount,
                'quantity'     => $quantity,
                'status'       => 'completed', // In real apps, this is 'pending' until Stripe calls back

                // Snapshot! If price changes later, this keeps the record accurate
                'metadata'     => [
                    'snapshot_price' => $item->price,
                    'product_name'   => $listing->title,
                    'condition'      => $item->condition
                ]
            ]);

            // 4. Decrement Stock
            $item->decrement('quantity', $quantity);

            return redirect()->route('transactions.receipt', $transaction->uuid)
                ->with('success', 'Item purchased!');
        });
    }

    // 3. Handle Donation
    public function donate(Request $request, Listing $listing)
    {
        $donation = $listing->listable;

        if (!$donation instanceof \App\Models\DonationListing) {
            abort(404);
        }

        return DB::transaction(function () use ($request, $listing, $donation) {

            $donation = $donation->lockForUpdate()->find($donation->id);

            $transaction = Transaction::create([
                'uuid'         => Str::uuid(),
                'user_id'      => Auth::id(),
                'payable_type' => get_class($donation),
                'payable_id'   => $donation->id,
                'type'         => 'donation',
                'amount'       => $request->amount,
                'status'       => 'completed',
                'metadata'     => [
                    'campaign_title' => $listing->title,
                    'donor_note'     => $request->input('note')
                ]
            ]);

            $donation->increment('amount_raised', $request->amount);
            $donation->increment('donors_count');

            return back()->with('success', 'Thank you for your donation!');
        });
    }

    // 4. Handle Investment
    public function invest(Request $request, Listing $listing)
    {
        $investment = $listing->listable;

        // Validate Shares
        $request->validate(['shares' => 'required|integer|min:1']);
        $sharesToBuy = $request->input('shares');

        if (!$investment instanceof \App\Models\InvestmentListing) {
            abort(404);
        }

        return DB::transaction(function () use ($request, $listing, $investment, $sharesToBuy) {

            // 1. Lock the investment row
            $investment = $investment->lockForUpdate()->find($investment->id);

            // 2. Check if enough shares exist
            if ($investment->shares_offered < $sharesToBuy) {
                return back()->withErrors(['shares' => 'Only ' . $investment->shares_offered . ' shares remaining.']);
            }

            $totalCost = $investment->share_price * $sharesToBuy;

            // 3. Create Transaction
            $transaction = Transaction::create([
                'uuid'         => Str::uuid(),
                'user_id'      => Auth::id(),
                'payable_type' => get_class($investment),
                'payable_id'   => $investment->id,
                'type'         => 'investment',
                'amount'       => $totalCost,
                'quantity'     => $sharesToBuy,
                'status'       => 'completed',
                'metadata'     => [
                    'share_price_at_booking' => $investment->share_price,
                    'project_name'           => $listing->title
                ]
            ]);

            // 4. Update Investment Metrics
            // We reduce the shares available, and increase amount raised
            $investment->decrement('shares_offered', $sharesToBuy);
            $investment->increment('amount_raised', $totalCost);
            $investment->increment('investors_count');

            return redirect()->route('transactions.portfolio')
                ->with('success', 'Investment successful!');
        });
    }


    public function index(Request $request)
    {
        $filters = $request->validate([
            'type' => 'nullable|string|in:buy_now,auction,donation,investment',
            'search' => 'nullable|string',
        ]);

        $transactions = \App\Models\Transaction::where('user_id', Auth::id())
            ->with(['payable.listing']) // Eager load the listing via the polymorphic relation
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
}
