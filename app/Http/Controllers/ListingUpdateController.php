<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingUpdateController extends Controller
{
    public function store(Request $request, Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $listing->updates()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => 'manual',
        ]);

        return $this->checkSuccess($listing, 'updated', 'home');
    }
}