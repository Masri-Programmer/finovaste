<?php

use App\Models\Category;
use App\Models\Listing;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

// --- Explicit Definitions (Custom Logic) ---

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push(__('breadcrumbs.home'), route('home'));
});

// Home > Categories > [Category Name]
Breadcrumbs::for('categories.show', function (BreadcrumbTrail $trail, Category $category) {
    $trail->parent('categories.index');
    $trail->push($category->name, route('categories.show', $category));
});

// Home > Listings > [Listing Title]
Breadcrumbs::for('listings.show', function (BreadcrumbTrail $trail, Listing $listing) {
    $trail->parent('listings.index');
    $trail->push($listing->title, route('listings.show', $listing));
});

// Home > Listings > [Listing Title] > Edit
Breadcrumbs::for('listings.edit', function (BreadcrumbTrail $trail, Listing $listing) {
    $trail->parent('listings.show', $listing);
    $trail->push('Edit', route('listings.edit', $listing));
});

// Settings Base (Virtual)
Breadcrumbs::for('settings', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(__('breadcrumbs.settings.index'), route('profile.edit'));
});

// --- Dynamic Generation ---

$routes = Route::getRoutes()->getRoutesByName();

foreach ($routes as $name => $route) {
    // Skip if already defined or not a GET request
    if (Breadcrumbs::exists($name) || !in_array('GET', $route->methods())) {
        continue;
    }
    
    // Skip internal/debug routes
    if (Str::startsWith($name, ['telescope.', '_debugbar.', 'ignition.', 'sanctum.'])) {
        continue;
    }

    Breadcrumbs::for($name, function (BreadcrumbTrail $trail, ...$params) use ($name) {
        // limit recursion/loops by defaulting to home
        $parentFound = false;
        
        // 1. Try to find a parent by segments (e.g. listings.create -> listings.index)
        $parts = explode('.', $name);
        while (count($parts) > 1) {
            array_pop($parts);
            $parentName = implode('.', $parts);
            
            // Try convention: parent is index? e.g. listings.create -> parent is listings.index
            if (!Breadcrumbs::exists($parentName)) {
                $parentName .= '.index';
            }

            if (Breadcrumbs::exists($parentName) && $parentName !== $name) {
                $trail->parent($parentName, ...$params); // Pass params just in case parent needs them
                $parentFound = true;
                break;
            }
        }
        
        // 2. Special case: if we are in settings.* but didn't match above loop (e.g. strict segments)
        // Ensure settings pages fall under the 'settings' virtual parent if not already caught
        if (!$parentFound && Str::startsWith($name, 'settings.') && Breadcrumbs::exists('settings')) {
             $trail->parent('settings');
             $parentFound = true;
        }

        // 3. Fallback to Home
        if (!$parentFound) {
            $trail->parent('home');
        }

        // Determine Title from properties or translation
        // Try 'breadcrumbs.route.name'
        $transKey = 'breadcrumbs.' . $name;
        if (Lang::has($transKey)) {
            $title = __($transKey);
        } else {
            // Fallback: Humanize the last segment
            $title = Str::title(str_replace(['-', '_'], ' ', Arr::last(explode('.', $name))));
        }

        $trail->push($title, route($name, $params));
    });
}
