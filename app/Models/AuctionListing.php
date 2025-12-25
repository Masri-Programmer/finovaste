<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

use Illuminate\Database\Eloquent\SoftDeletes;

class AuctionListing extends Model
{
    use HasFactory, SoftDeletes;

    // Remove 'listing_id' from fillable
    protected $fillable = [
        'start_price',
        'reserve_price',
        'purchase_price',
        'current_bid',
        'item_condition',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'start_price'   => 'decimal:2', 
        'reserve_price' => 'decimal:2',
        'purchase_price'=> 'decimal:2',
        'current_bid'   => 'decimal:2',
        'starts_at'     => 'datetime',
        'ends_at'       => 'datetime',
    ];

    /**
     * This works because the 'listings' table has 'listable_id'
     * pointing to THIS model's ID.
     */
    public function listing(): MorphOne
    {
        return $this->morphOne(Listing::class, 'listable');
    }
}
