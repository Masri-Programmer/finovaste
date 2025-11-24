<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Listing;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'street',
        'city',
        'state',
        'zip',
        'country',
        'latitude',
        'longitude',
    ];

    /**
     * Get the listings associated with the address.
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }
}
