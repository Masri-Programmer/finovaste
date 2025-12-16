<?php

namespace Database\Seeders;

use App\Models\AuctionListing;
use App\Models\PurchaseListing;
use App\Models\DonationListing;
use App\Models\InvestmentListing;
use App\Models\Listing;
use App\Models\ListingFaq;
use App\Models\Review; 
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Factories\AddressFactory;
use Kdabrow\SeederOnce\SeederOnce;

class ListingSeeder extends SeederOnce
{
    protected int $count = 3;

    public function run(): void
    {
        if (!User::count() || !\App\Models\Category::count()) {
            $this->command->error('Please seed users and categories before seeding listings.');
            return;
        }

        $this->command->info("Preparing to seed {$this->count} of each listing type in random order...");

        $listingTypes = [
            InvestmentListing::class,
            PurchaseListing::class,
            AuctionListing::class,
            DonationListing::class,
        ];

        $tasks = collect();

        foreach ($listingTypes as $type) {
            for ($i = 0; $i < $this->count; $i++) {
                $tasks->push($type);
            }
        }

        $bar = $this->command->getOutput()->createProgressBar($tasks->count());
        $bar->start();

        $tasks->shuffle()->each(function ($class) use ($bar) {
            // 1. Create the Specific Listing (Auction, Purchase, etc)
            $specificModel = $class::factory()
                ->withListing()
                ->create();
            
            // 2. Access the parent Listing model to attach FAQs and Reviews
            if ($specificModel->listing) {
                $this->seedFaqs($specificModel->listing);
                $this->seedReviews($specificModel->listing); // <--- Add Reviews
                $this->seedMedia($specificModel->listing);
            }

            $bar->advance();
        });

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Randomized listings seeded successfully.');

        $this->seedOriginalListings();
    }

    protected function seedOriginalListings(): void
    {
        $user = User::first();
        $category = \App\Models\Category::first();

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
        $listing = $investment->listing()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 'active',
            'title' => ['en' => 'Sustainable Tech Startup Investment', 'de' => 'Investition in nachhaltiges Tech-Startup'],
            'description' => ['en' => 'Invest in a promising tech startup...', 'de' => 'Investieren Sie in ein vielversprechendes Tech-Startup...'],
        ]);
        $this->seedFaqs($listing);
        $this->seedReviews($listing);
        $this->seedMedia($listing);

        // Buy Now Listing
        $purchase = PurchaseListing::create(['price' => 45000, 'quantity' => 1, 'condition' => 'new']);
        $listing = $purchase->listing()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 'active',
            'title' => ['en' => 'Luxury Apartment Downtown', 'de' => 'Luxuswohnung in der Innenstadt'],
            'description' => ['en' => 'A beautiful luxury apartment...', 'de' => 'Eine wunderschöne Luxuswohnung...'],
        ]);
        $this->seedFaqs($listing);
        $this->seedReviews($listing);
        $this->seedMedia($listing);

        // Auction Listing
        $auction = AuctionListing::create([
            'start_price' => 75000,
            'ends_at' => now()->addWeeks(2),
        ]);
        $listing = $auction->listing()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 'active',
            'title' => ['en' => 'Rare Collectible Art Auction', 'de' => 'Auktion für seltene Sammlerkunst'],
            'description' => ['en' => 'Bidding starts now...', 'de' => 'Das Bieten für dieses einzigartige Kunstwerk...'],
        ]);
        $this->seedFaqs($listing);
        $this->seedReviews($listing);
        $this->seedMedia($listing);

        // Donation Listing
        $donation = DonationListing::create(['donation_goal' => 25000]);
        $listing = $donation->listing()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 'active',
            'title' => ['en' => 'Community Park Renovation Fund', 'de' => 'Spendenfonds für die Renovierung des Gemeindeparks'],
            'description' => ['en' => 'Help us renovate the local community park...', 'de' => 'Helfen Sie uns, den örtlichen Gemeindepark zu renovieren...'],
        ]);
        $this->seedFaqs($listing);
        $this->seedReviews($listing);
        $this->seedMedia($listing);

        $this->command->info('Original listings re-seeded with FAQs and Reviews.');
    }

    /**
     * Helper to create 2 FAQs for a given listing.
     */
    private function seedFaqs(Listing $listing): void
    {
        ListingFaq::factory()
            ->count(2)
            ->create([
                'listing_id' => $listing->id,
                'user_id' => User::where('id', '!=', $listing->user_id)->inRandomOrder()->first()->id 
                             ?? User::factory(),
            ]);
    }

    private function seedReviews(Listing $listing): void
    {
        $potentialReviewers = User::where('id', '!=', $listing->user_id)
            ->inRandomOrder()
            ->take(rand(1, 4))
            ->get();

        if ($potentialReviewers->isEmpty()) {
            $potentialReviewers = collect([User::factory()->create()]);
        }

        foreach ($potentialReviewers as $reviewer) {
            Review::factory()->create([
                'listing_id' => $listing->id,
                'user_id' => $reviewer->id,
            ]);
        }
    }

    private function seedMedia(Listing $listing): void
    {
        $seedingPath = storage_path('app/seeding');
        if (!file_exists($seedingPath)) {
            mkdir($seedingPath, 0755, true);
        }

        $files = [
            'image_1.jpg' => 'https://loremflickr.com/640/480/business',
            'image_2.jpg' => 'https://loremflickr.com/640/480/city',
            'video.mp4' => 'https://raw.githubusercontent.com/intel-iot-devkit/sample-videos/master/classroom.mp4',
            'doc_1.pdf' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
            'doc_2.pdf' => 'https://www.africau.edu/images/default/sample.pdf',
        ];

        // Download files once if they don't exist
        foreach ($files as $name => $url) {
            $path = "$seedingPath/$name";
            if (!file_exists($path)) {
                try {
                    file_put_contents($path, file_get_contents($url));
                } catch (\Exception $e) {
                    // Fallback or ignore if download fails
                    continue;
                }
            }
        }

        try {
            if (file_exists("$seedingPath/image_1.jpg")) {
                $listing->addMedia("$seedingPath/image_1.jpg")
                    ->preservingOriginal()
                    ->toMediaCollection('images');
            }

            if (file_exists("$seedingPath/image_2.jpg")) {
                $listing->addMedia("$seedingPath/image_2.jpg")
                    ->preservingOriginal()
                    ->toMediaCollection('images');
            }
        } catch (\Exception $e) {
        }

        try {
            // Only add video to some listings
            if (rand(0, 1) && file_exists("$seedingPath/video.mp4")) {
                 $listing->addMedia("$seedingPath/video.mp4")
                    ->preservingOriginal()
                    ->toMediaCollection('videos');
            }
        } catch (\Exception $e) {
        }

        try {
            if (file_exists("$seedingPath/doc_1.pdf")) {
                $listing->addMedia("$seedingPath/doc_1.pdf")
                    ->preservingOriginal()
                    ->withCustomProperties(['name' => 'Generic Business Plan'])
                    ->toMediaCollection('documents');
            }

            if (file_exists("$seedingPath/doc_2.pdf")) {
                $listing->addMedia("$seedingPath/doc_2.pdf")
                    ->preservingOriginal()
                    ->withCustomProperties(['name' => 'Annual Report'])
                    ->toMediaCollection('documents');
            }
        } catch (\Exception $e) {
        }
    }
}