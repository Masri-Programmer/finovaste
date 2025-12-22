<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AddressController extends Controller
{
    /**
     * Show the user's addresses.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('settings/Addresses', [
            'addresses' => $request->user()->addresses,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Store a new address.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'zip' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $request->user()->addresses()->create($validated);

        return to_route('addresses.index')->with('status', 'address-created');
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, Address $address): RedirectResponse
    {
        // Ensure the user owns the address
        if ($address->addressable_id !== $request->user()->id || $address->addressable_type !== get_class($request->user())) {
            abort(403);
        }

        $validated = $request->validate([
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'zip' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $address->update($validated);

        return to_route('addresses.index')->with('status', 'address-updated');
    }

    /**
     * Set the specified address as primary.
     */
    public function setPrimary(Request $request, Address $address): RedirectResponse
    {
        // Ensure the user owns the address
        if ($address->addressable_id !== $request->user()->id || $address->addressable_type !== get_class($request->user())) {
            abort(403);
        }

        $address->update(['is_primary' => true]);

        return to_route('addresses.index')->with('status', 'address-primary-set');
    }

    /**
     * Remove the specified address.
     */
    public function destroy(Request $request, Address $address): RedirectResponse
    {
        // Ensure the user owns the address
        if ($address->addressable_id !== $request->user()->id || $address->addressable_type !== get_class($request->user())) {
            abort(403);
        }

        $address->delete();

        return to_route('addresses.index')->with('status', 'address-deleted');
    }
}
