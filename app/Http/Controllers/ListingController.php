<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use App\Models\InvestmentListing; // Assuming this model exists
use App\Models\AuctionListing;    // Existing model
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    // A mapping for the listing types to their specific models
    private $listableModels = [
        'Investment' => InvestmentListing::class,
        'Auction' => AuctionListing::class,
        // 'BuyNow' => BuyNowListing::class, // Add others as needed
        // 'Donation' => DonationListing::class,
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listings = Listing::with('listable')->get();
        return response()->json($listings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:' . implode(',', array_keys($this->listableModels)), // e.g., Investment, Auction
        ]);

        $listableType = $request->input('type');

        if (!isset($this->listableModels[$listableType])) {
            return response()->json(['message' => 'Invalid listing type provided.'], 422);
        }

        $listableModelClass = $this->listableModels[$listableType];

        DB::beginTransaction();
        try {
            $listable = $listableModelClass::create($this->getListableData($request, $listableType));

            $listing = $listable->listing()->create([
                'user_id' => auth()->id(),
                'category_id' => $request->input('category_id'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'slug' => Str::slug($request->input('title')) . '-' . time(),
                // ... other core listing fields
            ]);

            DB::commit();
            return response()->json(['message' => 'Listing created successfully.', 'listing' => $listing->load('listable')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Listing creation failed.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Listing $listing)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            // ... Add validation for core listing and listable-specific fields
        ]);

        DB::beginTransaction();
        try {
            $listing->update([
                'category_id' => $request->input('category_id', $listing->category_id),
                'title' => $request->input('title', $listing->title),
                'description' => $request->input('description', $listing->description),
                // ... other core listing fields
            ]);

            $listableType = class_basename($listing->listable_type);
            $listableData = $this->getListableData($request, $listableType, true); // true for update

            if (!empty($listableData)) {
                $listing->listable->update($listableData);
            }

            DB::commit();
            return response()->json(['message' => 'Listing updated successfully.', 'listing' => $listing->load('listable')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Listing update failed.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        DB::beginTransaction();
        try {
            $listing->listable->delete(); // Delete the specific listing type data
            $listing->delete(); // Delete the main listing entry

            DB::commit();
            return response()->json(['message' => 'Listing deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Listing deletion failed.', 'error' => $e->getMessage()], 500);
        }
    }

    private function getListableData(Request $request, string $listableType, bool $isUpdate = false): array
    {
        $data = [];
        $requiredKeys = [];

        switch ($listableType) {
            case 'Investment':
                $keys = ['capital_goal', 'min_investment', 'risk_percentage'];
                $data = $request->only($keys);
                if ($isUpdate) {
                    $data = array_filter($data, fn($value) => !is_null($value));
                }
                if (isset($data['capital_goal'])) $data['donation_goal'] = $data['capital_goal'];
                unset($data['capital_goal']);

                // Note: 'capitalRaised' and 'investors' would be dynamically updated or stored in other tables.
                // Here we focus on the static fields for creation/update.
                break;

            case 'Auction':
                $keys = ['start_price', 'reserve_price', 'ends_at'];
                $data = $request->only($keys);
                if ($isUpdate) {
                    $data = array_filter($data, fn($value) => !is_null($value));
                }

                // Map 'startingBid' (from your example) to 'start_price' (in your schema)
                if (isset($data['startingBid'])) $data['start_price'] = $data['startingBid'];
                unset($data['startingBid']);
                break;

                // Add other cases (BuyNow, Donation) here...
        }

        return $data;
    }
}
