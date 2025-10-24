<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // To ensure a clean slate, you can disable foreign key checks
        // and truncate the table before seeding.
        // \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        // Category::truncate();
        // \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $categories = [
            'Properties' => [
                'icon' => 'building-2',
                'children' => ['Commercial Real Estate', 'Residential Homes', 'Land Plots']
            ],
            'Vehicles' => [
                'icon' => 'car',
                'children' => ['Cars', 'Motorcycles', 'Boats', 'Aircraft']
            ],
            'Furniture' => [
                'icon' => 'sofa',
                'children' => ['Living Room', 'Bedroom', 'Office Furniture', 'Outdoor']
            ],
            'Electronics' => [
                'icon' => 'tv',
                'children' => ['Computers & Laptops', 'Mobile Phones', 'Cameras', 'Audio Equipment']
            ],
            'Art & Collectibles' => [
                'icon' => 'palette',
                'children' => ['Paintings', 'Sculptures', 'Antiques', 'Stamps & Coins']
            ],
            'Businesses for Sale' => [
                'icon' => 'store',
                'children' => ['Restaurants', 'Retail Stores', 'Tech Companies']
            ],
            'Startups' => [
                'icon' => 'rocket',
                'children' => ['FinTech', 'HealthTech', 'SaaS']
            ],
            'Campaigns' => [
                'icon' => 'megaphone',
                'children' => ['Charity', 'Community Projects', 'Fundraising']
            ],
        ];

        foreach ($categories as $categoryName => $details) {
            $parent = Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
                'description' => "Browse all listings under the {$categoryName} category.",
                'is_active' => true,
                'type' => 'listing', // Example type
                'icon' => $details['icon'],
            ]);

            foreach ($details['children'] as $childName) {
                Category::create([
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'description' => "Find {$childName} within the {$categoryName} category.",
                    'is_active' => true,
                    'type' => 'listing',
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
