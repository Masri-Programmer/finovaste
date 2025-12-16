<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AuctionListing;
use App\Models\BuyNowListing;
use App\Models\DonationListing;
use App\Models\InvestmentListing;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Listing extends Model implements HasMedia

{
    use HasTranslations, HasSlug, HasFactory, SoftDeletes, InteractsWithMedia;
    public $translatable = ['title', 'description'];
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
        'is_liked_by_current_user' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function listable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }
    /**
     * Get all of the listing's media.
     * A listing can have many images, videos, etc.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
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

    public function ownerAddress(): HasOneThrough
    {
        return $this->hasOneThrough(
            Address::class,
            User::class,
            'id',
            'addressable_id',
            'user_id',
            'id'
        )->where('addressable_type', User::class);
    }
    public function primaryAddress(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }
    /**
     * Get the reviews for the listing.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function likers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
    public function isLikedByCurrentUser(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->likers()->where('user_id', Auth::id())->exists();
    }

    public function getIsLikedByCurrentUserAttribute(): bool
    {
        return $this->isLikedByCurrentUser();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this
            ->addMediaCollection('documents')
            ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

        $this
            ->addMediaCollection('videos')
            ->acceptsMimeTypes(['video/mp4', 'video/quicktime']);
    }
    public function getPriceAttribute()
    {
        if ($this->listable && property_exists($this->listable, 'price')) {
            return $this->listable->price;
        }

        return null;
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->sharpen(10);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class)->orderBy('amount', 'desc');
    }
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function highestBid()
    {
        return $this->hasOne(Bid::class)->ofMany('amount', 'max');
    }

    public function auctionDetails()
    {
        return $this->morphTo(__FUNCTION__, 'listable_type', 'listable_id');
    }

    public function faqs()
    {
        return $this->hasMany(ListingFaq::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ListingSubscription::class);
    }
    public function updates()
    {
        return $this->hasMany(ListingUpdate::class)->latest();
    }
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        // 1. Search
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        });

        // 2. Category
        $query->when($filters['category'] ?? null, function ($query, $slug) {
            if ($slug !== 'all') {
                $query->whereHas('category', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            }
        });

        // 3. Types
        $query->when($filters['types'] ?? null, function ($query, $types) {
            $types = is_array($types) ? $types : [$types];

            $mappedClasses = [];
            foreach ($types as $type) {
                match ($type) {
                    'bid', 'auction' => $mappedClasses[] = AuctionListing::class,
                    'buy', 'purchase' => $mappedClasses[] = BuyNowListing::class,
                    'invest', 'investment' => $mappedClasses[] = InvestmentListing::class,
                    'donate', 'donation' => $mappedClasses[] = DonationListing::class,
                    default => null,
                };
            }

            if (!empty($mappedClasses)) {
                $query->whereIn('listable_type', $mappedClasses);
            }
        });

        $hasPriceFilter = ($filters['min_price'] ?? null) !== null || ($filters['max_price'] ?? null) !== null;

        $query->when($hasPriceFilter, function (Builder $query) use ($filters) {
            $min = $filters['min_price'] ?? 0;
            $max = $filters['max_price'] ?? 1000000;

            $query->whereHasMorph('listable', [BuyNowListing::class, AuctionListing::class, InvestmentListing::class], function (Builder $sq) use ($min, $max) {
                $sq->where(function (Builder $qq) use ($min, $max) {
                    $qq->orWhereBetween('price', [$min, $max]);

                    $qq->orWhereBetween('current_bid', [$min, $max]);

                    $qq->orWhereBetween('investment_goal', [$min, $max]);
                })
                    ->where(function (Builder $qq) {
                        $qq->whereNotNull('price')
                            ->orWhereNotNull('current_bid')
                            ->orWhereNotNull('investment_goal');
                    });
            });
        });

        // 5. Location
        $query->when($filters['city'] ?? null, function ($query, $city) {
            $query->whereHas('address', function ($q) use ($city) {
                $q->where('city', 'like', '%' . $city . '%');
            });
        });

        // 6. Sort
        $query->when($filters['sort'] ?? null, function ($query, $sort) {
            match ($sort) {
                'oldest' => $query->oldest(),
                default => $query->latest(),
            };
        }, fn($q) => $q->latest());

        return $query;
    }
}
