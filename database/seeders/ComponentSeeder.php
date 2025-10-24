<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Component;
use App\Models\ComponentItem;

class ComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $navComponent = Component::create([
            'name' => 'homepage_nav',
            'type' => 'navbar',
            'is_active' => true,
        ]);

        ComponentItem::create([
            'component_id' => $navComponent->id,
            'display_text' => 'Marketplace',
            'item_type' => 'link',
            'value' => 'marketplace',
            'sort_order' => 1,
        ]);

        ComponentItem::create([
            'component_id' => $navComponent->id,
            'display_text' => 'Investments',
            'item_type' => 'link',
            'value' => 'investments',
            'sort_order' => 2,
        ]);

        ComponentItem::create([
            'component_id' => $navComponent->id,
            'display_text' => 'Auctions',
            'item_type' => 'link',
            'value' => 'auctions',
            'sort_order' => 3,
        ]);

        ComponentItem::create([
            'component_id' => $navComponent->id,
            'display_text' => 'Donate',
            'item_type' => 'link',
            'value' => 'donate',
            'sort_order' => 4,
        ]);

        // You could also create other components, like a sidebar
    }
}
