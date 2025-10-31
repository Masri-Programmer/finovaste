<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Renders the Index (Read) page.
     */
    public function index(): Response
    {
        // Fetch top-level categories with their children, ordered by sort_order
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * Renders the Create page.
     */
    public function create(): Response
    {
        // Get all categories to be used in a "Parent Category" dropdown
        $parentCategories = Category::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Categories/Create', [
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Handles the Create logic.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'type' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'meta' => 'nullable|array',
        ]);

        // Automatically create the slug
        $validated['slug'] = Str::slug($validated['name']);

        // Set default values if not provided
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['type'] = $validated['type'] ?? 'default';

        Category::create($validated);

        return Redirect::route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     * Renders the Show page (optional, as Edit is often used instead).
     */
    public function show(Category $category): Response
    {
        // Eager load relationships for display
        $category->load('parent', 'children');

        return Inertia::render('Categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * Renders the Edit page.
     */
    public function edit(Category $category): Response
    {
        // Get all categories for the dropdown,
        // excluding the current category to prevent self-assignment.
        $parentCategories = Category::where('id', '!=', $category->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Categories/Edit', [
            'category' => $category,
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Handles the Update logic.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id), // Ignore self on unique check
            ],
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'type' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'meta' => 'nullable|array',
        ]);

        // Automatically update the slug if the name has changed
        $validated['slug'] = Str::slug($validated['name']);

        // Set default values if not provided
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['type'] = $validated['type'] ?? 'default';

        $category->update($validated);

        return Redirect::route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * Handles the Delete logic.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // This will trigger a soft delete because your model uses the SoftDeletes trait
        $category->delete();

        return Redirect::route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
