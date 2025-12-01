<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        $parentCategories = Category::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Categories/Create', [
            'parentCategories' => $parentCategories,
        ]);
    }

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

        $validated['slug'] = Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['type'] = $validated['type'] ?? 'default';

        $category = Category::create($validated);

        return $this->checkSuccess($category, 'created', 'categories.index');
    }

    public function show(Category $category): Response
    {
        $category->load('parent', 'children');

        return Inertia::render('Categories/Show', [
            'category' => $category,
        ]);
    }

    public function edit(Category $category): Response
    {
        $parentCategories = Category::where('id', '!=', $category->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Categories/Edit', [
            'category' => $category,
            'parentCategories' => $parentCategories,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id),
            ],
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'type' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'meta' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['type'] = $validated['type'] ?? 'default';

        $category->update($validated);

        return $this->checkSuccess($category, 'updated', 'categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return $this->checkSuccess($category, 'deleted', 'categories.index');
    }
}