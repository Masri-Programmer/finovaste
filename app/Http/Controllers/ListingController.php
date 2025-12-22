<?php

namespace App\Http\Controllers;

use App\Http\Requests\Listings\StoreListingRequest;
use App\Http\Requests\Listings\UpdateListingRequest;
use App\Models\Listing;
use App\Models\AuctionListing;
use App\Models\DonationListing;
use App\Models\PurchaseListing;
use App\Models\Category;
use App\Models\User;
use App\Services\ListingMediaService;
use App\Services\ListingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

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

        $listings = $this->listingService->getListings($filters);

        return Inertia::render('listings/Index', [
            'listings' => $listings,
            'filters' => $filters,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if(!$request->user()) {
            return redirect()->route('login');
        }
        return Inertia::render('listings/Create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreListingRequest $request, ListingMediaService $mediaService): RedirectResponse
    {
        $validatedData = $request->validated();

        $listing = null;
        $specificListing = null;

        try {
            DB::beginTransaction();

            switch ($validatedData['listing_type']) {
                case 'purchase':
                    $specificListing = PurchaseListing::create([
                        'price' => $validatedData['price'],
                        'quantity' => $validatedData['quantity'],
                        'condition' => $validatedData['condition'],
                    ]);
                    break;

                case 'auction':
                    $specificListing = AuctionListing::create([
                        'start_price' => $validatedData['start_price'],
                        'reserve_price' => $validatedData['reserve_price'],
                        'purchase_price' => $validatedData['purchase_price'],
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

            return $this->checkSuccess($listing, 'created', 'home');

        } catch (Throwable $e) {
            DB::rollBack();

            return $this->checkError('messages.errors.generic_user', $e);
        }
    }

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
            'updates',
        ]);

        $listing->loadCount([
            'likers as is_liked_by_current_user' => function ($query) {
                $query->where('user_id', Auth::id());
            },
            'bids'
        ]);

        $listing->load(['faqs' => function ($q) use ($listing) {
            $userId = Auth::id();

            if ($userId !== $listing->user_id) {
                $q->where(function ($query) use ($userId) {
                    $query->where(function ($sub) {
                        $sub->whereNotNull('answer')
                            ->where('is_visible', true);
                    });

                    if ($userId) {
                        $query->orWhere('user_id', $userId);
                    }
                });
            }

            $q->with('user:id,name');
         }]);

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

        // Paginate reviews
        $reviews = $listing->reviews()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(10);

        $listingArray['reviews'] = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'profile_photo_url' => $review->user->profile_photo_url ?? null,
                ],
                'rating' => $review->rating,
                'body' => $review->body,
                'created_at' => $review->created_at,
                'time_ago' => $review->created_at?->diffForHumans(),
                'can_edit' => Auth::id() === $review->user_id,
            ];
        });

        $listingArray['next_page_url'] = $reviews->nextPageUrl();

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

        $listingData = $listing->toArray();
        $listingData['listable'] = $listing->listable;
        $listingData['title'] = $listing->getTranslations('title');
        $listingData['description'] = $listing->getTranslations('description');
        $listingData['media'] = $listing->media;

        return Inertia::render('listings/Edit', [
            'listing' => $listingData,
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

            return $this->checkSuccess($listing, );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->checkError('messages.errors.generic_user', $e);
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

            return $this->checkSuccess($listing, 'deleted', 'listings.index');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->checkError('messages.errors.generic_user', $e);
        }
    }

public function like(Request $request, Listing $listing): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        if ($listing->isLikedByCurrentUser()) {
            return redirect()->back();
        }

        try {
            $listing->likers()->syncWithoutDetaching(Auth::id());
            $listing->increment('likes_count');

            return $this->checkSuccess($listing, 'liked');

        } catch (\Exception $e) {
            return $this->checkError('messages.errors.generic_user', $e);
        }
    }

    public function unlike(Request $request, Listing $listing): RedirectResponse
    {
        try {
            $detached = $listing->likers()->detach(Auth::id());

            if ($detached) {
                $listing->decrement('likes_count');
            }

            return $this->checkSuccess($listing, 'unliked');

        } catch (\Exception $e) {
            return $this->checkError('messages.errors.generic_user', $e);
        }
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