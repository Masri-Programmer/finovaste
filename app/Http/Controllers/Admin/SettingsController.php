<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * Display the admin settings page.
     *
     * The settings are already shared via HandleInertiaRequests,
     * so we just need to render the component.
     */
    public function show(): Response
    {
        return Inertia::render('admin/settings/Show');
    }

    /**
     * Update the general application settings.
     */
    public function update(Request $request, GeneralSettings $settings): RedirectResponse
    {
        // 1. Validate the request data
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_active' => ['required', 'boolean'],
            'per_page' => ['nullable', 'number', 'min:1'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'contact_email' => ['nullable', 'email', 'max:255'],
        ]);

        // 2. Update the settings properties
        $settings->site_name = $validated['site_name'];
        $settings->site_active = $validated['site_active'];
        $settings->logo_url = $validated['logo_url'];
        $settings->per_page = $validated['per_page'];
        $settings->contact_email = $validated['contact_email'];

        $settings->save();

        return redirect()->back()->with('message', 'Settings updated successfully.');
    }
}
