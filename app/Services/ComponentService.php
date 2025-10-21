<?php

namespace App\Services;

use App\Models\Component;

class ComponentService
{
    /**
     * Fetches component items and transforms the flat list into a nested tree structure.
     */
    public function getStructuredComponent(string $name): array
    {
        $component = Component::with(['items' => function ($query) {
            $query->orderBy('sort_order');
        }])
            ->where('name', $name)
            ->where('is_active', true)
            ->first();

        if (!$component) {
            return [];
        }

        return $this->buildTree($component->items->toArray());
    }

    /**
     * Recursive function to build the tree structure from a flat array.
     */
    protected function buildTree(array $items, $parentId = null): array
    {
        $tree = [];
        foreach ($items as $item) {
            if ($item['parent_id'] === $parentId) {

                // 💡 Translation Logic
                if ($item['translation_key']) {
                    // Ensure display_text is translated if a key exists
                    $item['display_text'] = __($item['translation_key']);
                }

                $children = $this->buildTree($items, $item['id']);
                if (!empty($children)) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }
}
