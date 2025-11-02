<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class AuctionListing extends Model
{
    use HasFactory;

    /**
     * We don't need timestamps for this specific table
     * if the main listing table's timestamps are sufficient.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start_price',
        'reserve_price',
        'buy_it_now_price',
        'current_bid',
        'starts_at',
        'ends_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Get the parent listing model.
     */
    public function listing(): MorphOne
    {
        return $this->morphOne(Listing::class, 'listable');
    }
}
