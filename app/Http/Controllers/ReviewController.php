<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'body' => 'required|string|min:3|max:1000',
        ]);

        // Optional: Prevent user from reviewing their own listing
        if ($listing->user_id === Auth::id()) {
            return back()->with('error', 'You cannot review your own listing.');
        }

        // Optional: Check if user already reviewed this listing
        $existingReview = $listing->reviews()->where('user_id', Auth::id())->first();
        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this listing.');
        }

        $listing->reviews()->create([
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'body' => $validated['body'],
            // 'parent_id' => $request->parent_id // Uncomment if you implement replies later
        ]);

        return back()->with('success', 'Review posted successfully.');
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, Review $review)
    {
        // Ensure the authenticated user owns the review
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'body' => 'required|string|min:3|max:1000',
        ]);

        $review->update($validated);

        return back()->with('success', 'Review updated successfully.');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        // Ensure the authenticated user owns the review or is an admin
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}