<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
