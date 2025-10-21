<?php
// app/Models/Component.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Component extends Model
{
    protected $fillable = ['name', 'type', 'is_active', 'meta'];
    protected $casts = ['meta' => 'array'];

    public function items(): HasMany
    {
        return $this->hasMany(ComponentItem::class);
    }
}
