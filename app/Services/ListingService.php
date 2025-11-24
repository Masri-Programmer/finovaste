<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\BuyNowListing;
use App\Models\AuctionListing;
use App\Models\DonationListing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Listings\StoreListingRequest;
use App\Http\Requests\Listings\UpdateListingRequest;
use Throwable;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListingService
{
    protected $mediaService;

    /**
     * Fetch the category tree with counts.
     */
    public function getCategories(): \Illuminate\Support\Collection
    {
        $allCategories = Category::whereNull('parent_id')
            ->with('children')
            ->withCount('listings')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $allCategories->map(function ($category) {
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
    }

    /**
     * Filter and paginate listings.
     */
    public function getListings(array $filters): LengthAwarePaginator
    {
        $listings = Listing::query()
            ->with(['listable', 'user', 'category', 'media'])
            ->filter($filters)
            ->paginate(12)
            ->withQueryString();

        $listings->getCollection()->transform(function ($listing) {
            $listing->image_url = $listing->getFirstMediaUrl('images');
            unset($listing->media);
            $listing->append('is_liked_by_current_user');
            return $listing;
        });

        return $listings;
    }
    /**
     * Create a new listing.
     * @throws \Exception
     */
    public function createListing(StoreListingRequest $request): Listing
    {
        $validatedData = $request->validated();

        return DB::transaction(function () use ($request, $validatedData) {
            $specificListing = $this->createSpecificListing($validatedData);

            $listing = $specificListing->listing()->create([
                'user_id' => Auth::id(),
                'category_id' => $validatedData['category_id'],
                'expires_at' => $validatedData['expires_at'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
            ]);

            $this->mediaService->handleUploads($request, $listing);

            return $listing;
        });
    }

    /**
     * Update an existing listing.
     * @throws \Exception
     */
    public function updateListing(UpdateListingRequest $request, Listing $listing): Listing
    {
        $commonData = $request->getCommonData();
        $specificData = $request->getSpecificData();
        $mediaToDelete = $request->validated('media_to_delete', []);

        return DB::transaction(function () use ($request, $listing, $commonData, $specificData, $mediaToDelete) {
            $listing->update($commonData);

            if ($listing->listable) {
                $listing->listable->update($specificData);
            }

            $this->mediaService->handleDeletions($mediaToDelete);
            $this->mediaService->handleUploads($request, $listing);

            return $listing->fresh();
        });
    }

    private function createSpecificListing(array $data)
    {
        switch ($data['listing_type']) {
            case 'buy_now':
                return BuyNowListing::create([
                    'price' => $data['price'],
                    'quantity' => $data['quantity'],
                    'condition' => $data['condition'],
                ]);
            case 'auction':
                return AuctionListing::create([
                    'start_price' => $data['start_price'],
                    'reserve_price' => $data['reserve_price'],
                    'buy_it_now_price' => $data['buy_it_now_price'],
                    'starts_at' => $data['starts_at'],
                    'ends_at' => $data['ends_at'],
                ]);
            case 'donation':
                return DonationListing::create([
                    'donation_goal' => $data['donation_goal'],
                    'is_goal_flexible' =>  $data['is_goal_flexible'],
                ]);
            default:
                throw new \Exception("Invalid listing type: {$data['listing_type']}");
        }
    }
}
