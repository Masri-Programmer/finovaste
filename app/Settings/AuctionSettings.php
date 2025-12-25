<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AuctionSettings extends Settings
{
    // Anti-Sniping Logic
    public int $sniper_protection_seconds;
    public int $sniper_extension_seconds;

    // Time Management
    public int $processing_buffer_seconds;

    // Fees & Money
    public float $default_commission_percent;
    public float $listing_fee;

    // Logic: Array of increments
    public array $bid_increments;

    public static function group(): string
    {
        return 'auction';
    }
}
