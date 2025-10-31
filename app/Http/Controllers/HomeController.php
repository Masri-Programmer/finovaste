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
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

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
