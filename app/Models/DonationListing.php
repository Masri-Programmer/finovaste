<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class DonationListing extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'donation_listings';

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'donation_goal',
        'amount_raised',
        'donors_count',
        'is_goal_flexible',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'donation_goal' => 'decimal:2',
        'amount_raised' => 'decimal:2',
        'is_goal_flexible' => 'boolean',
    ];

    /**
     * Get the parent listing model.
     */
    public function listing(): MorphOne
    {
        return $this->morphOne(Listing::class, 'listable');
    }
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'payable');
    }
}
