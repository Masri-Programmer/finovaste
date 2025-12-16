<?php

namespace Database\Seeders;

use App\Models\Bid;
use App\Models\Listing;
use App\Models\User;
use App\Models\AuctionListing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Kdabrow\SeederOnce\SeederOnce;
class BidSeeder extends SeederOnce
{
    public function run(): void
    {
        $auctionListings = Listing::with('listable')
            ->where('listable_type', AuctionListing::class)
            ->get();

        $users = User::all();

        if ($users->count() < 2) {
            $this->command->info('Not enough users to seed bids. Create users first.');
            return;
        }

        $this->command->info('Simulating bidding wars...');

        foreach ($auctionListings as $listing) {

            if (rand(1, 100) <= 30) {
                continue;
            }

            $numberOfBids = rand(3, 15);

            $currentPrice = $listing->listable->start_price;

            $bidTime = now()->subDays(2);

            for ($i = 0; $i < $numberOfBids; $i++) {

                $increment = rand(100, 5000) / 100;
                $currentPrice += $increment;

                $bidder = $users->where('id', '!=', $listing->user_id)->random();

                $bidTime->addMinutes(rand(10, 180));

                Bid::create([
                    'listing_id' => $listing->id,
                    'user_id'    => $bidder->id,
                    'amount'     => $currentPrice,
                    'status'     => 'active',
                    'created_at' => $bidTime,
                    'updated_at' => $bidTime,
                ]);
            }

            $listing->listable->update([
                'current_bid' => $currentPrice
            ]);
        }
    }
}
