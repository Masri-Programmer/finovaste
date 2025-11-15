<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     *
     */
    public function index(): Response
    {
        $categories = Category::with('parent', 'children')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/categories/Index', [
            'categories' => $categories,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    /**
     *
     */
    public function create(): Response
    {
        $parentCategories = Category::orderBy('name')->get();

        return Inertia::render('admin/categories/Create', [
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     *
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategorie erfolgreich erstellt.');
    }

    /**
     *
     */
    public function show(Category $category): Response
    {
        $category->load('parent', 'children', 'listings');

        return Inertia::render('admin/categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     *
     */
    public function edit(Category $category): Response
    {
        $parentCategories = Category::where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/categories/Edit', [
            'category' => $category,
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     *
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategorie erfolgreich aktualisiert.');
    }

    /**
     *
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            if ($category->children()->count() > 0) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Löschen nicht möglich: Diese Kategorie hat Unterkategorien. Bitte weisen Sie diese zuerst neu zu.');
            }

            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategorie erfolgreich gelöscht.');
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Beim Löschen der Kategorie ist ein Fehler aufgetreten.');
        }
    }
}
