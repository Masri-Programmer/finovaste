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
use App\Services\ListingMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Throwable;

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

    private function getCategoriesForForm(): \Illuminate\Support\Collection
    {
        return Category::orderBy('name')->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->getTranslations('name'),
            ];
        });
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        dd(1);
        return Inertia::render('listings/Create', [
            'categories' => $this->getCategoriesForForm(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreListingRequest $request, ListingMediaService $mediaService): RedirectResponse
    {
        $validatedData = $request->validated();

        // Log::info('ListingController@store: Request received.', [
        //     'user_id' => auth()->id(),
        //     'listing_type' => $request->listing_type,
        //     'validated_data_keys' => array_keys($request->except(['images', 'documents', 'videos'])),
        // ]);

        $listing = null;
        $specificListing = null;

        try {
            DB::beginTransaction();

            // === STEP 1: Create the Specific Listing Type ===
            switch ($validatedData['listing_type']) {
                case 'buy_now':
                    $specificListing = BuyNowListing::create([
                        'price' => $validatedData['price'],
                        'quantity' => $validatedData['quantity'],
                        'condition' => $validatedData['condition'],
                    ]);
                    break;

                case 'auction':
                    $specificListing = AuctionListing::create([
                        'start_price' => $validatedData['start_price'],
                        'reserve_price' => $validatedData['reserve_price'],
                        'buy_it_now_price' => $validatedData['buy_it_now_price'],
                        'starts_at' => $validatedData['starts_at'],
                        'ends_at' => $validatedData['ends_at'],
                    ]);
                    break;

                case 'donation':
                    $specificListing = DonationListing::create([
                        'donation_goal' => $validatedData['donation_goal'],
                        'is_goal_flexible' => $validatedData['is_goal_flexible'],
                    ]);
                    break;

                default:
                    throw new \Exception("Invalid listing type: {$validatedData['listing_type']}");
            }

            $listing = $specificListing->listing()->create([
                'user_id' => auth()->id(),
                'category_id' => $validatedData['category_id'],
                'expires_at' => $validatedData['expires_at'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
            ]);

            $filesAttached = 0;
            if ($request->hasFile('images')) $filesAttached += count($request->file('images'));
            if ($request->hasFile('documents')) $filesAttached += count($request->file('documents'));
            if ($request->hasFile('videos')) $filesAttached += count($request->file('videos'));

            if ($filesAttached > 0) {
                Log::info("Attaching $filesAttached total media items via service.");
            }

            $mediaService->handleUploads($request, $listing);

            DB::commit();
            Log::info("SUCCESS: Transaction committed for listing ID: {$listing->id}");
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('CRITICAL: Failed to create listing.', [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Could not save your listing.']);
        }

        return redirect()->route('home');
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

        $listingData = $listing->toArray();
        $listingData['listable'] = $listing->listable;
        $listingData['title'] = $listing->getTranslations('title');
        $listingData['description'] = $listing->getTranslations('description');

        return Inertia::render('listings/Edit', [
            'listing' => $listingData,
            'categories' => $this->getCategoriesForForm(),
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateListingRequest $request, Listing $listing, ListingMediaService $mediaService)
    {
        $commonData = $request->getCommonData();
        $specificData = $request->getSpecificData();
        $mediaToDelete = $request->validated('media_to_delete', []);

        try {
            DB::beginTransaction();

            $listing->update($commonData);

            if ($listing->listable) {
                $listing->listable->update($specificData);
            }
            $mediaService->handleDeletions($mediaToDelete);
            $mediaService->handleUploads($request, $listing);

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
