<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuctionListing;
use App\Models\DonationListing;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->query();

        // Eager load 'listable' to get auction/donation details efficiently
        $listings = Listing::with(['listable', 'user', 'category'])
            ->filter($filters)
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('admin/listings/Index', [
            'listings' => $listings,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pass necessary data for dropdowns (Categories, etc.)
        return Inertia::render('admin/listings/Create', [
            'categories' => \App\Models\Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Basic Validation
        $validated = $request->validate([
            'title' => 'required|array', // JSON translatable
            'description' => 'required|array',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:auction,donation',
            
            // Conditional Validation (add more specific rules as needed)
            'start_price' => 'required_if:type,auction|numeric|min:0',
            'ends_at' => 'required_if:type,auction|date|after:now',
            'target' => 'required_if:type,donation|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 2. Create the Child (Specific Logic)
            $listable = null;

            if ($request->type === 'auction') {
                $listable = AuctionListing::create([
                    'start_price' => $request->start_price,
                    'reserve_price' => $request->reserve_price,
                    'item_condition' => $request->item_condition ?? 'new',
                    'starts_at' => $request->starts_at ?? now(),
                    'ends_at' => $request->ends_at,
                ]);
            } elseif ($request->type === 'donation') {
                $listable = DonationListing::create([
                    'target' => $request->target,
                    'is_capped' => $request->boolean('is_capped'),
                    'requires_verification' => $request->boolean('requires_verification'),
                ]);
            }

            // 3. Create the Parent (Wrapper) & Link It
            // We use the relationship on the CHILD to create the PARENT
            // This ensures listable_type and listable_id are set automatically.
            $listable->listing()->create([
                'user_id' => auth()->id(), // Assuming Admin is creating, or pass user_id
                'category_id' => $request->category_id,
                'address_id' => $request->address_id, // Optional
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'status' => 'active', // Default status
                
                // CRITICAL: Sync expiration date for the Scheduler Command
                'expires_at' => ($request->type === 'auction') ? $request->ends_at : null,
            ]);

            DB::commit();

            return redirect()->route('admin.listings.index')
                ->with('success', 'Listing created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create listing: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        $listing->load(['listable', 'media', 'user', 'category', 'reviews']);

        return Inertia::render('admin/listings/Show', [
            'listing' => $listing,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listing $listing)
    {
        // Load the child relationship so the form can populate 'start_price' etc.
        $listing->load(['listable', 'media']);

        return Inertia::render('admin/listings/Edit', [
            'listing' => $listing,
            'categories' => \App\Models\Category::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Listing $listing)
    {
        $listing->load('listable');

        try {
            DB::beginTransaction();

            // 1. Update Parent Fields
            $listing->update($request->only([
                'title', 
                'description', 
                'status', 
                'category_id',
                'is_featured'
            ]));

            // 2. Update Child Fields based on type
            if ($listing->type === 'auction' && $listing->listable) {
                $listing->listable->update($request->only([
                    'start_price', 
                    'reserve_price', 
                    'ends_at',
                    'item_condition'
                ]));
                
                // Sync dates if they changed
                if ($request->filled('ends_at')) {
                    $listing->update(['expires_at' => $request->ends_at]);
                }
            } 
            elseif ($listing->type === 'donation' && $listing->listable) {
                $listing->listable->update($request->only([
                    'target', 
                    'is_capped',
                    'requires_verification'
                ]));
            }

            DB::commit();

            return redirect()->back()->with('success', 'Listing updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update listing: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        try {
            DB::beginTransaction();
            
            // 1. Delete the Child Details first
            // The ?-> operator prevents crashing if listable is already missing
            $listing->listable?->delete();
            
            // 2. Delete the Parent Wrapper
            $listing->delete();
            
            DB::commit();

            return redirect()->route('admin.listings.index')
                ->with('success', 'Listing deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete listing: ' . $e->getMessage());
        }
    }
}