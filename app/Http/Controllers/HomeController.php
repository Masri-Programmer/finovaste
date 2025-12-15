<?php

namespace App\Http\Controllers;

use App\Services\ListingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    protected ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search'    => 'nullable|string|max:100',
            'category'  => 'nullable|string|max:100',
            'types'     => 'nullable|string',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'city'      => 'nullable|string|max:100',
            'sort'      => 'nullable|string|in:latest,oldest,price-low,price-high,popular',
        ]);

        $categories = $this->listingService->getCategories();
        $listings = $this->listingService->getListings($filters);

        return Inertia::render('Homepage', [
            'categories' => $categories,
            'listings' => $listings,
            'filters' => $filters,
        ]);
    }

    public function faq(): Response {
        return Inertia::render('Faq');
    }

    public function about(): Response {
        return Inertia::render('About');
    }
}
