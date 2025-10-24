<?php

// app/Models/ComponentItem.php

namespace App\Models;

use App\Models\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComponentItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'component_id',
        'parent_id',
        'translation_key',
        'display_text',
        'item_type',
        'value',
        'content',
        'sort_order',
        'meta',
    ];

    protected $casts = ['meta' => 'array'];
    /**
     * Get the parent item (for sub-menus).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ComponentItem::class, 'parent_id');
    }

    /**
     * Get the child items (for main menu items).
     */
    public function children(): HasMany
    {
        return $this->hasMany(ComponentItem::class, 'parent_id');
    }

    /**
     * Get the main component this item belongs to.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }
}
