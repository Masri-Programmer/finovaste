<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Traits\HasAppMessages;
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

        return Inertia::render('admin/categories/Index');
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
        $category = Category::create($request->validated());

        return $this->checkSuccess($category, 'created', 'admin.categories.index');
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

        return $this->checkSuccess($category, 'updated', 'admin.categories.index');
    }

    /**
     *
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            if ($category->children()->count() > 0) {
                return $this->checkError('messages.errors.category_has_children');
            }

            $category->delete();

            return $this->checkSuccess($category, 'deleted', 'admin.categories.index');
        } catch (\Exception $e) {
            return $this->checkError('messages.errors.generic_user', $e);
        }
    }
}
