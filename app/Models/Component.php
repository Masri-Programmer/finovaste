<?php
// app/Models/Component.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Component extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'type', 'is_active', 'meta'];
    protected $casts = ['meta' => 'array'];

    public function items(): HasMany
    {
        return $this->hasMany(ComponentItem::class);
    }
}
