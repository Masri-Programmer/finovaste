<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUuids
{
    /**
     * Boot the trait, adding a creating observer.
     *
     * This will automatically generate a UUID for the model's uuid attribute
     * if it is not already set.
     */
    protected static function bootHasUuids(): void
    {
        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
