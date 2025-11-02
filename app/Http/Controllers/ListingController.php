<?php

namespace App\Http\Controllers;

use App\Http\Requests\Listings\StoreListingRequest;
use App\Http\Requests\Listings\UpdateListingRequest;
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
            ->paginate(50);

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
    public function store(StoreListingRequest $request)
    {
        $commonData = $request->getCommonData();
        $specificData = $request->getSpecificData();
        $listable = null;

        try {
            DB::beginTransaction();

            switch ($commonData['listing_type']) {

                case 'investment':
                    $listable = InvestmentListing::create($specificData);
                    break;

                case 'auction':
                    $listable = AuctionListing::create($specificData);
                    break;

                case 'donation':
                    $listable = DonationListing::create($specificData);
                    break;

                case 'buy_now':
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
                'description' => $commonData['description'],
                'status' => 'pending',
                'expires_at' => $commonData['expires_at'] ?? null,
                // 'meta' => $request->input('meta', []),
            ]);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $listing->addMedia($file)->toMediaCollection('images');
                }
            }

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $listing->addMedia($file)->toMediaCollection('documents');
                }
            }

            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $file) {
                    $listing->addMedia($file)->toMediaCollection('videos');
                }
            }
            DB::commit();

            return redirect()->route('home')->with('success', 'Listing created successfully.');
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

        $allCategories = Category::orderBy('name')->get();
        $categories = $allCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->getTranslations('name'),
            ];
        });

        $listingData = $listing->toArray();
        $listingData['listable'] = $listing->listable;
        $listingData['title'] = $listing->getTranslations('title');
        $listingData['description'] = $listing->getTranslations('description');

        return Inertia::render('listings/Edit', [
            'listing' => $listingData,
            'categories' => $categories,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateListingRequest $request, Listing $listing) // <-- 1. Type-hinted
    {
        $commonData = $request->getCommonData();
        $specificData = $request->getSpecificData();


        try {
            DB::beginTransaction();

            $listing->update($commonData);

            if ($listing->listable) {
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
            $listing->listable->delete();
            $listing->delete();
            DB::commit();

            return redirect()->route('listings.index')->with('success', 'Listing deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete listing: ' . $e->getMessage());
        }
    }
}
