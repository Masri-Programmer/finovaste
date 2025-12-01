<?php

namespace App\Services;

use App\Models\Listing;

class ListingUpdateService
{
    /**
     * Trigger a system update (Investment, Bid, Status Change)
     */
    public static function system(Listing $listing, string $key, array $attributes = [])
    {
        return $listing->updates()->create([
            'type' => 'system',
            'translation_key' => $key,
            'attributes' => $attributes,
            'content' => null, 
        ]);
    }

    /**
     * Trigger a manual update (Owner writing a post)
     */
    public static function manual(Listing $listing, string $title, string $content)
    {
        return $listing->updates()->create([
            'type' => 'manual',
            'title' => $title,
            'content' => $content,
        ]);
    }
}