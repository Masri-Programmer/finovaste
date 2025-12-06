<?php

use App\Models\Category;
use App\Models\Listing;

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

// Home > Dashboard
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', route('dashboard'));
});

// --- Categories ---

// Home > Categories
Breadcrumbs::for('categories.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Categories', route('categories.index'));
});

// Home > Categories > [Category Name]
Breadcrumbs::for('categories.show', function (BreadcrumbTrail $trail, Category $category) {
    $trail->parent('categories.index');
    // Assuming your Category model has a 'name' attribute
    $trail->push($category->name, route('categories.show', $category));
});

// --- Listings ---

// Home > Listings
Breadcrumbs::for('listings.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Listings', route('listings.index'));
});

// Home > Listings > Create
Breadcrumbs::for('listings.create', function (BreadcrumbTrail $trail) {
    $trail->parent('listings.index');
    $trail->push('Create Listing', route('listings.create'));
});

// Home > Liked Listings
Breadcrumbs::for('listings.liked', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('My Liked Listings', route('listings.liked'));
});

// Home > Listings > [Listing Title]
Breadcrumbs::for('listings.show', function (BreadcrumbTrail $trail, Listing $listing) {
    $trail->parent('listings.index');
    // Assuming your Listing model has a 'title' attribute
    $trail->push($listing->title, route('listings.show', $listing));
});

// Home > Listings > [Listing Title] > Edit
Breadcrumbs::for('listings.edit', function (BreadcrumbTrail $trail, Listing $listing) {
    $trail->parent('listings.show', $listing);
    $trail->push('Edit', route('listings.edit', $listing));
});

// --- Settings ---

// Home > Settings (Virtual parent for all settings pages)
Breadcrumbs::for('settings', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Settings', '/settings/profile'); // Links to the default profile page
});

// Home > Settings > Profile
Breadcrumbs::for('profile.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push('Profile', route('profile.edit'));
});

// Home > Settings > Password
Breadcrumbs::for('password.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push('Change Password', route('password.edit'));
});

// Home > Settings > Appearance
Breadcrumbs::for('appearance.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push('Appearance', route('appearance.edit'));
});

// Home > Settings > Languages
Breadcrumbs::for('languages.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push('Languages', route('languages.edit'));
});

// Home > Settings > Two-Factor Authentication
Breadcrumbs::for('two-factor.show', function (BreadcrumbTrail $trail) {
    $trail->parent('settings');
    $trail->push('Two-Factor Authentication', route('two-factor.show'));
});

// --- Auth ---

// Home > Register
Breadcrumbs::for('register', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Register', route('register'));
});

// Home > Forgot Password
Breadcrumbs::for('password.request', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Forgot Password', route('password.request'));
});

// Home > Forgot Password > Reset Password
Breadcrumbs::for('password.reset', function (BreadcrumbTrail $trail) {
    $trail->parent('password.request');
    $trail->push('Reset Password', route('password.reset'));
});

// Home > Verify Email
Breadcrumbs::for('verification.notice', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Verify Email', route('verification.notice'));
});
// Home > Transaction History
Breadcrumbs::for('transactions.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Transaction History', route('transactions.index'));
});
