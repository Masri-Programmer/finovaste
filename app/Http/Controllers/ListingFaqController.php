<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingFaqController extends Controller
{
    // User asks a question
    public function store(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
        ]);

        // Save question in current locale
        $faq = new ListingFaq();
        $faq->listing_id = $listing->id;
        $faq->user_id = Auth::id();

        // If owner asks, it's visible immediately, otherwise hidden until verified
        $faq->is_visible = (Auth::id() === $listing->user_id);

        $faq->setTranslation('question', app()->getLocale(), $validated['question']);
        $faq->save();

        return back()->with('success', 'Question submitted!');
    }

    // Owner answers or verifies
    public function update(Request $request, Listing $listing, ListingFaq $faq)
    {
        // Security check
        if (Auth::id() !== $listing->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'answer' => 'nullable|string',
            'is_visible' => 'boolean'
        ]);

        if ($request->has('answer')) {
            $faq->setTranslation('answer', app()->getLocale(), $validated['answer']);
        }

        if ($request->has('is_visible')) {
            $faq->is_visible = $validated['is_visible'];
        }

        $faq->save();

        return back()->with('success', 'FAQ updated.');
    }

    public function destroy(Listing $listing, ListingFaq $faq)
    {
        if (Auth::id() !== $listing->user_id) abort(403);
        $faq->delete();
        return back();
    }
}
