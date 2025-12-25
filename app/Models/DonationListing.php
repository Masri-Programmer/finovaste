<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany; // Don't forget this import
use Illuminate\Database\Eloquent\SoftDeletes;

class DonationListing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'donation_listings';

    // REMOVE 'listing_id' from here
    protected $fillable = [
        'target',
        'raised',
        'donors_count',
        'is_capped',
        'requires_verification',
    ];

    protected $casts = [
        'target' => 'decimal:2',
        'raised' => 'decimal:2',
        'donors_count' => 'integer',
        'is_capped' => 'boolean',
        'requires_verification' => 'boolean',
    ];

    /**
     * Correct: This connects back to the Listing that holds "listable_id" = this->id
     */
    public function listing(): MorphOne
    {
        return $this->morphOne(Listing::class, 'listable');
    }

    /**
     * Good: Assuming your transactions table has 'payable_type' and 'payable_id'
     */
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'payable');
    }
    
    // --- HELPER FOR UI --- //
    
    /**
     * Calculate percentage for the progress bar
     */
    public function getProgressPercentAttribute(): int
    {
        if ($this->target <= 0) return 0;
        return min(100, round(($this->raised / $this->target) * 100));
    }
}