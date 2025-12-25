<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Traits\HasAppMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AddressController extends Controller
{
    use HasAppMessages;

    /**
     * Show the user's addresses.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('settings/Addresses', [
            'addresses' => $request->user()->addresses,
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

        $address = $request->user()->addresses()->create($validated);

        return $this->checkSuccess($address, 'created', 'addresses.index');
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, Address $address): RedirectResponse
    {
        // Ensure the user owns the address
        if ($address->addressable_id !== $request->user()->id || $address->addressable_type !== get_class($request->user())) {
            abort(403, __('messages.errors.unauthorized'));
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

        return $this->checkSuccess($address, 'updated', 'addresses.index');
    }

    /**
     * Set the specified address as primary.
     */
    public function setPrimary(Request $request, Address $address): RedirectResponse
    {
        // Ensure the user owns the address
        if ($address->addressable_id !== $request->user()->id || $address->addressable_type !== get_class($request->user())) {
            abort(403, __('messages.errors.unauthorized'));
        }

        $address->update(['is_primary' => true]);

        return $this->checkSuccess($address, 'updated', 'addresses.index');
    }

    /**
     * Remove the specified address.
     */
    public function destroy(Request $request, Address $address): RedirectResponse
    {
        // Ensure the user owns the address
        if ($address->addressable_id !== $request->user()->id || $address->addressable_type !== get_class($request->user())) {
            abort(403, __('messages.errors.unauthorized'));
        }

        $address->delete();

        return $this->checkSuccess($address, 'deleted', 'addresses.index');
    }
}

