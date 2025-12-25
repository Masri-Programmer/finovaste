<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing;
use App\Models\AuctionListing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateListingStatuses extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'listings:update-statuses';

    /**
     * The console command description.
     */
    protected $description = 'Check for expired listings and update their status (Sold/Expired)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        // 1. Find all ACTIVE listings that have passed their expiration time
        // We use chunkById to handle large datasets without running out of memory
        $count = 0;
        
        Listing::where('status', 'active')
            ->where('expires_at', '<=', $now)
            ->with('listable') // Eager load the auction/donation details
            ->chunkById(100, function ($listings) use (&$count) {
                
                foreach ($listings as $listing) {
                    try {
                        $this->processListing($listing);
                        $count++;
                    } catch (\Exception $e) {
                        Log::error("Failed to process listing ID {$listing->id}: " . $e->getMessage());
                    }
                }
            });

        $this->info("Processed {$count} listings.");
    }

    private function processListing(Listing $listing)
    {
        DB::transaction(function () use ($listing) {
            
            // --- SCENARIO A: It is an AUCTION ---
            if ($listing->listable_type === AuctionListing::class) {
                $auction = $listing->listable;
                
                // Logic: Did it sell?
                // 1. Must have a bid
                // 2. If reserve_price exists, current_bid must be >= reserve_price
                $hasBids = $auction->current_bid > 0;
                $reserveMet = is_null($auction->reserve_price) || ($auction->current_bid >= $auction->reserve_price);

                if ($hasBids && $reserveMet) {
                    $listing->update(['status' => 'sold']);
                    // TODO: Fire Event -> SendEmailToWinner($listing)
                    $this->info("Auction {$listing->id} SOLD for {$auction->current_bid}");
                } else {
                    $listing->update(['status' => 'expired']);
                    // TODO: Fire Event -> SendEmailToSeller($listing) (Item didn't sell)
                    $this->info("Auction {$listing->id} EXPIRED (Reserve not met or no bids)");
                }
            }
            
            // --- SCENARIO B: It is a DONATION ---
            elseif ($listing->listable_type === \App\Models\DonationListing::class) {
                // Donations usually just expire or mark as 'completed'
                $listing->update(['status' => 'completed']); 
            }

            // --- SCENARIO C: Generic Fallback ---
            else {
                $listing->update(['status' => 'expired']);
            }
        });
    }
}