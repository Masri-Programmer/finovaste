<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AuctionListing;
use App\Models\BuyNowListing;
use App\Models\DonationListing;
use App\Models\InvestmentListing;

class Listing extends Model

{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'category_id',
        'address_id',
        'title',
        'slug',
        'description',
        'status',
        'is_featured',
        'published_at',
        'expires_at',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function listable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get all of the listing's media.
     * A listing can have many images, videos, etc.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get the user that owns the listing.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category for the listing.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the address for the listing.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the reviews for the listing.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    public function listing()
    {
        return $this->morphOne(Listing::class, 'listable');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        // 1. --- Search Filter ---
        // Searches title and description
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        });

        // 2. --- Category Filter ---
        // Assumes you are passing the category 'slug' in the URL
        $query->when($filters['category'] ?? null, function ($query, $slug) {
            $query->whereHas('category', function ($query) use ($slug) {
                $query->where('slug', $slug);
            });
        });

        // 3. --- Listing Type Filter ---
        // Filters by the polymorphic 'listable_type'
        $query->when($filters['type'] ?? null, function ($query, $type) {
            $listableModel = match ($type) {
                'auction' => AuctionListing::class,
                'buy_now' => BuyNowListing::class,
                'investment' => InvestmentListing::class,
                'donation' => DonationListing::class,
                default => null,
            };

            if ($listableModel) {
                $query->where('listable_type', $listableModel);
            }
        });

        // 4. --- Sort Filter ---
        $query->when($filters['sort'] ?? null, function ($query, $sort) {
            if ($sort === 'oldest') {
                $query->oldest(); // Sorts by created_at ascending
            }
            // Add more sorts like 'price_asc', 'price_desc' here
        }, function ($query) {
            // Default sort if 'sort' is not provided
            $query->latest(); // Sorts by created_at descending
        });

        return $query;
    }
}
