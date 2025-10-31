<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the homepage with categories and filterable listings.
     */
    public function index(Request $request): Response
    {
        $allCategories = Category::whereNull('parent_id')
            ->with('children')
            ->withCount('listings')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categories = $allCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'slug' => $category->slug,
                'icon' => $category->icon,

                'name' => $category->getTranslations('name'),

                'children' => $category->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'slug' => $child->slug,
                        'name' => $child->getTranslations('name'),
                    ];
                })
            ];
        });

        $filters = $request->validate([
            'search' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'type' => 'nullable|string|in:auction,donation,buy_now,investment',
            'sort' => 'nullable|string|in:latest,oldest',
        ]);
        $listings = Listing::query()
            ->with(['listable', 'user', 'category'])
            ->filter($filters)
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Homepage', [
            'categories' => $categories,
            'listings' => $listings,
            'filters' => $filters,
        ]);
    }
}
