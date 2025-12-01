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
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ListingMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use App\Services\ListingService;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use Throwable;
use Illuminate\Support\Facades\Cache;

class ListingController extends Controller
{
    protected ListingService $listingService;
    protected ListingMediaService $mediaService;

    public function __construct(ListingService $listingService, ListingMediaService $mediaService)
    {
        $this->listingService = $listingService;
        $this->mediaService = $mediaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

        return Inertia::render('listings/Index', [
            'categories' => $categories,
            'listings' => $listings,
            'filters' => $filters,
        ]);
    }

    private function getCategoriesForForm(): \Illuminate\Support\Collection
    {
        return Cache::remember('categories_for_form', 60 * 60, function () {
            return Category::orderBy('name')->get()->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->getTranslations('name'),
                ];
            });
        });
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
        //     'user_id' => Auth::id(),
        //     'listing_type' => $request->listing_type,
        //     'validated_data_keys' => array_keys($request->except(['images', 'documents', 'videos'])),
        // ]);

        $listing = null;
        $specificListing = null;

        try {
            DB::beginTransaction();

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
                'user_id' => Auth::id(),
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
                'user_id' => Auth::id(),
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
        $listing->load([
            'listable' => function ($morph) {
                $morph->withCount('transactions');
            }, 
            'user', 
            'category', 
            'address', 
            'media',
            'reviews.user'
        ]);

        $listing->loadCount([
            'likers as is_liked_by_current_user' => function ($query) {
                $query->where('user_id', Auth::id());
            },
            'bids'
        ]);

        $listing->load(['faqs' => function ($q) use ($listing) {
            $userId = Auth::id();
            
            // If owner, show all. Otherwise filter.
            if ($userId !== $listing->user_id) {
                $q->where(function ($query) use ($userId) {
                    // Public: Show if answered AND visible
                    $query->where(function ($sub) {
                        $sub->whereNotNull('answer')
                            ->where('is_visible', true);
                    });
                    
                    // Asker: Also show their own questions (even if unanswered/hidden)
                    if ($userId) {
                        $query->orWhere('user_id', $userId);
                    }
                });
            }
            
            $q->with('user:id,name');
        }]);

        // 2. Prepare Media Data (Existing logic)
        $mediaData = [
            'images' => $listing->getMedia('images')->map(fn($item) => [
                'id' => $item->id,
                'url' => $item->getUrl(),
                'thumbnail' => $item->getUrl('thumb'),
                'mime_type' => $item->mime_type,
            ]),
            'videos' => $listing->getMedia('videos')->map(fn($item) => [
                'id' => $item->id,
                'url' => $item->getUrl(),
                'mime_type' => $item->mime_type,
            ]),
            'documents' => $listing->getMedia('documents')->map(fn($item) => [
                'id' => $item->id,
                'url' => $item->getUrl(),
                'file_name' => $item->file_name,
                'size' => $item->human_readable_size,
            ]),
        ];

        $listingArray = $listing->toArray();
        $listingArray['media'] = $mediaData;

        // 3. Format Reviews for the UI
        $listingArray['reviews'] = $listing->reviews->sortByDesc('created_at')->values()->map(function ($review) {
            return [
                'id' => $review->id,
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    // Assuming you might have a profile_photo_url or similar
                    'profile_photo_url' => $review->user->profile_photo_url ?? null, 
                ],
                'rating' => $review->rating,
                'body' => $review->body,
                'created_at' => $review->created_at,
                'time_ago' => $review->created_at->diffForHumans(),
                'can_edit' => Auth::id() === $review->user_id,
            ];
        });

        return Inertia::render('listings/Show', [
            'listing' => $listingArray,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listing $listing)
    {
        $listing->load(['listable', 'media']);

        $allCategories = Category::orderBy('name')->get();

        $listingData = $listing->toArray();
        $listingData['listable'] = $listing->listable;
        $listingData['title'] = $listing->getTranslations('title');
        $listingData['description'] = $listing->getTranslations('description');

        // Pass the loaded media data to the frontend
        $listingData['media'] = $listing->media;

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

    public function like(Request $request, Listing $listing): RedirectResponse
    {
        if ($listing->isLikedByCurrentUser()) {
            return redirect()->back();
        }
        try {
            $listing->likers()->syncWithoutDetaching(Auth::id());
            $listing->increment('likes_count');
        } catch (\Exception $e) {
            Log::error("Failed to like listing {$listing->id} for user " . Auth::id(), [
                'message' => $e->getMessage()
            ]);
            return redirect()->back()->with('error');
        }

        return redirect()->back()->with('success', 'Listing liked!');
    }

    public function unlike(Request $request, Listing $listing): RedirectResponse
    {
        try {
            $detached = $listing->likers()->detach(Auth::id());
            if ($detached) {
                $listing->decrement('likes_count');
            }
        } catch (\Exception $e) {
            Log::error("Failed to unlike listing {$listing->id} for user " . Auth::id(), [
                'message' => $e->getMessage()
            ]);
            return redirect()->back()->with('error',);
        }

        return redirect()->back()->with('success', 'Listing unliked.');
    }

    public function liked(Request $request)
    {
        $user = $request->user();

        $listings = $user->likedListings()
            ->with(['listable', 'user', 'category', 'media'])
            ->latest('listing_user.created_at')
            ->paginate(12)
            ->withQueryString();

        $listings->getCollection()->transform(function ($listing) {
            $listing->append('is_liked_by_current_user');

            $listing->image_url = $listing->getFirstMediaUrl('images', 'thumb');

            unset($listing->media);
            return $listing;
        });

        return Inertia::render('listings/Liked', [
            'listings' => $listings,
        ]);
    }


    public function userListings(Request $request, User $user)
    {
        $filters = $request->query();

        $listings = $user->listings()
            ->filter($filters)
            ->with(['listable', 'user', 'category'])
            ->latest()
            ->paginate(50);

        return Inertia::render('listings/users/Index', [
            'listings' => $listings,
            'filters' => $filters,
            'user' => $user->only('id', 'name'),
            'hide_filters' => true,
        ]);
    }
}
