<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Kdabrow\SeederOnce\SeederOnce;

class CategorySeeder extends SeederOnce
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
            [
                'name' => ['en' => 'Properties', 'de' => 'Immobilien'],
                'icon' => 'building-2',
                'children' => [
                    ['name' => ['en' => 'Commercial Real Estate', 'de' => 'Gewerbeimmobilien']],
                    ['name' => ['en' => 'Residential Homes', 'de' => 'Wohnhäuser']],
                    ['name' => ['en' => 'Land Plots', 'de' => 'Grundstücke']],
                ]
            ],
            [
                'name' => ['en' => 'Vehicles', 'de' => 'Fahrzeuge'],
                'icon' => 'car',
                'children' => [
                    ['name' => ['en' => 'Cars', 'de' => 'Autos']],
                    ['name' => ['en' => 'Motorcycles', 'de' => 'Motorräder']],
                    ['name' => ['en' => 'Boats', 'de' => 'Boote']],
                    ['name' => ['en' => 'Aircraft', 'de' => 'Flugzeuge']],
                ]
            ],
            [
                'name' => ['en' => 'Furniture', 'de' => 'Möbel'],
                'icon' => 'sofa',
                'children' => [
                    ['name' => ['en' => 'Living Room', 'de' => 'Wohnzimmer']],
                    ['name' => ['en' => 'Bedroom', 'de' => 'Schlafzimmer']],
                    ['name' => ['en' => 'Office Furniture', 'de' => 'Büromöbel']],
                    ['name' => ['en' => 'Outdoor', 'de' => 'Außenmöbel']],
                ]
            ],
            [
                'name' => ['en' => 'Electronics', 'de' => 'Elektronik'],
                'icon' => 'tv',
                'children' => [
                    ['name' => ['en' => 'Computers & Laptops', 'de' => 'Computer & Laptops']],
                    ['name' => ['en' => 'Mobile Phones', 'de' => 'Handys']],
                    ['name' => ['en' => 'Cameras', 'de' => 'Kameras']],
                    ['name' => ['en' => 'Audio Equipment', 'de' => 'Audiogeräte']],
                ]
            ],
            [
                'name' => ['en' => 'Art & Collectibles', 'de' => 'Kunst & Sammlerstücke'],
                'icon' => 'palette',
                'children' => [
                    ['name' => ['en' => 'Paintings', 'de' => 'Gemälde']],
                    ['name' => ['en' => 'Sculptures', 'de' => 'Skulpturen']],
                    ['name' => ['en' => 'Antiques', 'de' => 'Antiquitäten']],
                    ['name' => ['en' => 'Stamps & Coins', 'de' => 'Briefmarken & Münzen']],
                ]
            ],
            [
                'name' => ['en' => 'Businesses for Sale', 'de' => 'Unternehmen zum Verkauf'],
                'icon' => 'store',
                'children' => [
                    ['name' => ['en' => 'Restaurants', 'de' => 'Restaurants']],
                    ['name' => ['en' => 'Retail Stores', 'de' => 'Einzelhandelsgeschäfte']],
                    ['name' => ['en' => 'Tech Companies', 'de' => 'Tech-Unternehmen']],
                ]
            ],
            [
                'name' => ['en' => 'Startups', 'de' => 'Startups'],
                'icon' => 'rocket',
                'children' => [
                    ['name' => ['en' => 'FinTech', 'de' => 'FinTech']],
                    ['name' => ['en' => 'HealthTech', 'de' => 'HealthTech']],
                    ['name' => ['en' => 'SaaS', 'de' => 'SaaS']],
                ]
            ],
            [
                'name' => ['en' => 'Campaigns', 'de' => 'Kampagnen'],
                'icon' => 'megaphone',
                'children' => [
                    ['name' => ['en' => 'Charity', 'de' => 'Wohltätigkeit']],
                    ['name' => ['en' => 'Community Projects', 'de' => 'Gemeinschaftsprojekte']],
                    ['name' => ['en' => 'Fundraising', 'de' => 'Spendensammlungen']],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $parentDescription = [
                'en' => "Browse all listings under the {$categoryData['name']['en']} category.",
                'de' => "Durchsuchen Sie alle Angebote in der Kategorie {$categoryData['name']['de']}."
            ];

            $parent = Category::create([
                'name'        => $categoryData['name'],
                'slug'        => Str::slug($categoryData['name']['en']),
                'description' => $parentDescription,
                'is_active'   => true,
                'type'        => 'listing',
                'icon'        => $categoryData['icon'],
            ]);

            foreach ($categoryData['children'] as $childData) {
                $childDescription = [
                    'en' => "Find {$childData['name']['en']} within the {$categoryData['name']['en']} category.",
                    'de' => "Finden Sie {$childData['name']['de']} in der Kategorie {$categoryData['name']['de']}."
                ];

                Category::create([
                    'name'        => $childData['name'],
                    'slug'        => Str::slug($childData['name']['en']),
                    'description' => $childDescription,
                    'is_active'   => true,
                    'type'        => 'listing',
                    'parent_id'   => $parent->id,
                ]);
            }
        }
    }
}
