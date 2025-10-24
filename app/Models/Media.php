<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'collection_name',
        'path',
        'file_name',
        'mime_type',
        'size',
        'order_column',
    ];

    /**
     * Get the parent mediable model (Listing, Review, etc.).
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
