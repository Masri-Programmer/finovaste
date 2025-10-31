<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\AuctionListing;
use App\Models\BuyNowListing;
use App\Models\Category;
use App\Models\DonationListing;
use App\Models\InvestmentListing;
use App\Models\User;
use Database\Factories\AddressFactory;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $category = Category::first();

        if (! $user || ! $category) {
            $this->command->error('Please seed users and categories before seeding listings.');

            return;
        }

        $address = $user->addresses()->first();

        if (! $address) {
            $address = $user->addresses()->create(
                AddressFactory::new()->make()->toArray()
            );
        }

        // 1. Create an Investment Listing
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
            // 'type' => 'investment', // <-- THIS LINE WAS REMOVED
            'title' => [
                'en' => 'Sustainable Tech Startup Investment',
                'de' => 'Investition in nachhaltiges Tech-Startup',
            ],
            'description' => [
                'en' => 'Invest in a promising tech startup focused on renewable energy solutions. Seeking capital for product scaling.',
                'de' => 'Investieren Sie in ein vielversprechendes Tech-Startup mit Fokus auf erneuerbare Energielösungen. Kapital für Produktskalierung gesucht.',
            ],
        ]);

        // 2. Create a Buy Now Listing
        $buyNow = BuyNowListing::create([
            'price' => 45000,
        ]);

        $buyNow->listing()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 'active',
            // 'type' => 'buy_now', // <-- THIS LINE WAS REMOVED
            'title' => [
                'en' => 'Luxury Apartment Downtown',
                'de' => 'Luxuswohnung in der Innenstadt',
            ],
            'description' => [
                'en' => 'A beautiful luxury apartment in the heart of the city. Available for immediate purchase.',
                'de' => 'Eine wunderschöne Luxuswohnung im Herzen der Stadt. Zum sofortigen Kauf verfügbar.',
            ],
        ]);

        // 3. Create an Auction Listing
        $auction = AuctionListing::create([
            'start_price' => 75000,
            'ends_at' => now()->addWeeks(2),
        ]);

        $auction->listing()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 'active',
            'title' => [
                'en' => 'Rare Collectible Art Auction',
                'de' => 'Auktion für seltene Sammlerkunst',
            ],
            'description' => [
                'en' => 'Bidding starts now for this one-of-a-kind art piece from a renowned artist. Auction ends in two weeks.',
                'de' => 'Das Bieten für dieses einzigartige Kunstwerk eines renommierten Künstlers beginnt jetzt. Die Auktion endet in zwei Wochen.',
            ],
        ]);
        // 4. Create a Donation Listing
        $donation = DonationListing::create([
            'donation_goal' => 25000,
        ]);

        $donation->listing()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 'active',
            // 'type' => 'donation', // <-- THIS LINE WAS REMOVED
            'title' => [
                'en' => 'Community Park Renovation Fund',
                'de' => 'Spendenfonds für die Renovierung des Gemeindeparks',
            ],
            'description' => [
                'en' => 'Help us renovate the local community park. All donations go directly to buying new equipment and planting trees.',
                'de' => 'Helfen Sie uns, den örtlichen Gemeindepark zu renovieren. Alle Spenden fließen direkt in den Kauf neuer Geräte und das Pflanzen von Bäumen.',
            ],
        ]);
    }
}
