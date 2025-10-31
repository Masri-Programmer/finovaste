<?php

namespace App\Http\Controllers;

// Import all your listable models
use App\Models\Listing;
use App\Models\AuctionListing;
use App\Models\DonationListing;
use App\Models\BuyNowListing;
use App\Models\InvestmentListing;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listings = Listing::with(['listable', 'user', 'category'])
            ->latest()
            ->paginate(10);

        return Inertia::render('listings/Index', [
            'listings' => $listings,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allCategories = Category::orderBy('name')->get();
        $categories = $allCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->getTranslations('name'),
            ];
        });
        return Inertia::render('listings/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $commonData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'listing_type' => 'required|string|in:auction,donation,buy_now,investment',
        ]);

        $listable = null;

        try {
            DB::beginTransaction();

            switch ($commonData['listing_type']) {

                case 'investment':
                    $specificData = $request->validate([
                        'investment_goal' => 'required|numeric|min:0',
                        'minimum_investment' => 'required|numeric|min:0',
                        'shares_offered' => 'required|integer|min:1',
                        'share_price' => 'required|numeric|min:0',
                    ]);
                    $listable = InvestmentListing::create($specificData);
                    break;

                case 'auction':
                    $specificData = $request->validate([
                        'start_price' => 'required|numeric|min:0',
                        'ends_at' => 'required|date|after:now',
                        'reserve_price' => 'nullable|numeric|gte:start_price',
                        'buy_it_now_price' => 'nullable|numeric|gte:start_price',
                    ]);
                    $listable = AuctionListing::create($specificData);
                    break;

                case 'donation':
                    $specificData = $request->validate([
                        'donation_goal' => 'required|numeric|min:0',
                        'is_goal_flexible' => 'boolean',
                    ]);
                    $listable = DonationListing::create($specificData);
                    break;

                case 'buy_now':
                    $specificData = $request->validate([
                        'price' => 'required|numeric|min:0',
                        'quantity' => 'required|integer|min:1',
                        'condition' => 'nullable|string|max:100',
                    ]);
                    $listable = BuyNowListing::create($specificData);
                    break;
            }

            if (!$listable) {
                throw new \Exception('Invalid listing type provided.');
            }

            $listing = $listable->listing()->create([
                'user_id' => auth()->id(),
                'category_id' => $commonData['category_id'],
                'title' => $commonData['title'],
                'slug' => Str::slug($commonData['title']) . '-' . uniqid(),
                'description' => $commonData['description'],
                'status' => 'pending',
                // 'meta' => $request->input('meta', []),
            ]);
            DB::commit();

            return redirect()->route('listings.index')->with('success', 'Listing created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create listing: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        $listing->load(['listable', 'user', 'category', 'address']);

        return Inertia::render('listings/Show', [
            'listing' => $listing,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listing $listing)
    {
        $listing->load('listable');

        return Inertia::render('listings/Edit', [
            'listing' => $listing,
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Listing $listing)
    {
        // 1. --- Validate Common Fields ---
        $commonData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            DB::beginTransaction();

            // 2. --- Update the Main Listing ---
            $listing->update($commonData);

            // 3. --- Update the Specific 'listable' Model ---
            if ($listing->listable instanceof InvestmentListing) {
                $specificData = $request->validate([
                    'investment_goal' => 'required|numeric|min:0',
                    'minimum_investment' => 'required|numeric|min:0',
                    'shares_offered' => 'required|integer|min:1',
                    'share_price' => 'required|numeric|min:0',
                ]);
                $listing->listable->update($specificData);
            } elseif ($listing->listable instanceof AuctionListing) {
                $specificData = $request->validate([
                    'start_price' => 'required|numeric|min:0',
                    'ends_at' => 'required|date',
                    'reserve_price' => 'nullable|numeric|gte:start_price',
                    'buy_it_now_price' => 'nullable|numeric|gte:start_price',
                ]);
                $listing->listable->update($specificData);
            } elseif ($listing->listable instanceof DonationListing) {
                $specificData = $request->validate([
                    'donation_goal' => 'required|numeric|min:0',
                    'is_goal_flexible' => 'boolean',
                ]);
                $listing->listable->update($specificData);
            } elseif ($listing->listable instanceof BuyNowListing) {
                $specificData = $request->validate([
                    'price' => 'required|numeric|min:0',
                    'quantity' => 'required|integer|min:1',
                    'condition' => 'nullable|string|max:100',
                ]);
                $listing->listable->update($specificData);
            }

            DB::commit();

            return redirect()->route('listings.show', $listing)->with('success', 'Listing updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update listing: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        try {
            DB::beginTransaction();
            // Delete the specific model first
            $listing->listable->delete();
            // Then delete the main listing
            $listing->delete();
            DB::commit();

            return redirect()->route('listings.index')->with('success', 'Listing deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete listing: ' . $e->getMessage());
        }
    }
}
