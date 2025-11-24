<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\ComponentSeeder as SeedersComponentSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RolesAndPermissionsSeeder::class,
            // AddressSeeder::class,
            SeedersComponentSeeder::class,
            CategorySeeder::class,
            ListingSeeder::class,
            BidSeeder::class,
        ]);
    }
}
