<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listing $listing)
    {
        $listing->load(['listable', 'media']);

        return Inertia::render('admin/listings/Edit', [
            'listing' => $listing,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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

            return redirect()->route('admin.listings.index')->with('success', 'Listing deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete listing: ' . $e->getMessage());
        }
    }
}
