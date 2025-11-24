<?php

namespace Database\Seeders;

use App\Models\AuctionListing;
use App\Models\BuyNowListing;
use App\Models\DonationListing;
use App\Models\InvestmentListing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Factories\AddressFactory;
use Illuminate\Support\Collection;

class ListingSeeder extends Seeder
{
    /**
     * The number of listings to create for each type.
     */
    protected int $count = 5;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check for dependencies first (Users and Categories)
        if (!User::count() || !\App\Models\Category::count()) {
            $this->command->error('Please seed users and categories before seeding listings.');
            return;
        }

        $this->command->info("Preparing to seed {$this->count} of each listing type in random order...");

        $listingTypes = [
            InvestmentListing::class,
            BuyNowListing::class,
            AuctionListing::class,
            DonationListing::class,
        ];

        // 2. Create a collection of tasks
        $tasks = collect();

        foreach ($listingTypes as $type) {
            // For each type, add it to the list $count times
            for ($i = 0; $i < $this->count; $i++) {
                $tasks->push($type);
            }
        }

        // 3. Shuffle the tasks and execute them
        // This interleaves the IDs and Created_at timestamps in the database
        $bar = $this->command->getOutput()->createProgressBar($tasks->count());
        $bar->start();

        $tasks->shuffle()->each(function ($class) use ($bar) {
            $class::factory()
                ->withListing() // Your custom trait
                ->create();

            $bar->advance();
        });

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Randomized listings seeded successfully.');

        // Restore original specific listings
        $this->seedOriginalListings();
    }

    /**
     * Recreates the four specific listings from your original seeder logic.
     */
    protected function seedOriginalListings(): void
    {
        $user = User::first();
        $category = \App\Models\Category::first();

        // Ensure user has an address for the manual seeding
        $address = $user->addresses()->first();
        if (! $address) {
            $address = $user->addresses()->create(
                AddressFactory::new()->make()->toArray()
            );
        }

        // Investment Listing
        $investment = InvestmentListing::create([
            'investment_goal' => 150000,
            'minimum_investment' => 1500,
            'shares_offered' => 1000,
            'share_price' => 150,
        ]);
        $investment->listing()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 'active',
            'title' => ['en' => 'Sustainable Tech Startup Investment', 'de' => 'Investition in nachhaltiges Tech-Startup'],
            'description' => ['en' => 'Invest in a promising tech startup...', 'de' => 'Investieren Sie in ein vielversprechendes Tech-Startup...'],
        ]);

        // Buy Now Listing
        $buyNow = BuyNowListing::create(['price' => 45000, 'quantity' => 1, 'condition' => 'new']); // Added missing fillable values
        $buyNow->listing()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 'active',
            'title' => ['en' => 'Luxury Apartment Downtown', 'de' => 'Luxuswohnung in der Innenstadt'],
            'description' => ['en' => 'A beautiful luxury apartment...', 'de' => 'Eine wunderschöne Luxuswohnung...'],
        ]);

        // Auction Listing
        $auction = AuctionListing::create([
            'start_price' => 75000,
            'ends_at' => now()->addWeeks(2),
        ]);
        $auction->listing()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 'active',
            'title' => ['en' => 'Rare Collectible Art Auction', 'de' => 'Auktion für seltene Sammlerkunst'],
            'description' => ['en' => 'Bidding starts now...', 'de' => 'Das Bieten für dieses einzigartige Kunstwerk...'],
        ]);

        // Donation Listing
        $donation = DonationListing::create(['donation_goal' => 25000]);
        $donation->listing()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 'active',
            'title' => ['en' => 'Community Park Renovation Fund', 'de' => 'Spendenfonds für die Renovierung des Gemeindeparks'],
            'description' => ['en' => 'Help us renovate the local community park...', 'de' => 'Helfen Sie uns, den örtlichen Gemeindepark zu renovieren...'],
        ]);
        $this->command->info('Original listings re-seeded.');
    }
}
