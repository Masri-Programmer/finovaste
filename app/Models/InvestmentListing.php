<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class InvestmentListing extends Model
{
    use HasFactory;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'investment_goal',
        'minimum_investment',
        'shares_offered',
        'share_price',
        'amount_raised',
        'investors_count',
    ];

    /**
     * Get the parent listing model.
     */
    public function listing(): MorphOne
    {
        return $this->morphOne(Listing::class, 'listable');
    }
}
