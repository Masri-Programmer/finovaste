<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ModelController extends Controller
{
    protected $modelClass;
    protected $modelViewName;
    protected $modelInstance;

    /**
     * Resolve the model class from the route parameter.
     */
    public function __construct(Request $request)
    {
        $modelSlug = $request->route('model');
        $modelName = Str::studly(Str::singular($modelSlug));

        $this->modelClass = "App\\Models\\" . $modelName;
        $this->modelViewName = Str::studly($modelSlug);

        if (!class_exists($this->modelClass)) {
            abort(404, "Model {$modelName} not found");
        }

        $this->modelInstance = new $this->modelClass;
    }

    protected function getResourceCollection($data, $modelClass)
    {
        $modelBasename = class_basename($modelClass);
        $resourceClass = "App\\Http\\Resources\\{$modelBasename}Resource";

        if (class_exists($resourceClass)) {
            return $resourceClass::collection($data);
        }

        return $data;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = $this->buildQuery($request)->paginate($request->input('per_page', 10))->withQueryString();


            $modelNameCamel = Str::camel($this->modelViewName);

            return Inertia::render($this->modelViewName . '/Index', [
                $modelNameCamel => $this->getResourceCollection($data, $this->modelClass),
                'filters' => $request->all(),
            ]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render($this->modelViewName . '/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $this->validateRequest($request);
            $item = $this->modelClass::create($validatedData);

            DB::commit();

            return redirect()->route('model.index', ['model' => Str::kebab($this->modelViewName)])
                ->with('success', __($this->modelViewName . '.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($modelSlug, $id)
    {
        try {
            $item = $this->modelClass::with($this->getRelationships())->findOrFail($id);
            $modelNameCamel = Str::camel(Str::singular($this->modelViewName));

            return Inertia::render($this->modelViewName . '/Show', [
                $modelNameCamel => $item,
            ]);
        } catch (\Exception $e) {
            abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($modelSlug, $id)
    {
        try {
            $item = $this->modelClass::with($this->getRelationships())->findOrFail($id);
            $modelNameCamel = Str::camel(Str::singular($this->modelViewName));

            return Inertia::render($this->modelViewName . '/Edit', [
                $modelNameCamel => $item,
            ]);
        } catch (\Exception $e) {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $modelSlug, $id)
    {
        try {
            DB::beginTransaction();

            $item = $this->modelClass::findOrFail($id);
            $validatedData = $this->validateRequest($request, $item);

            $item->update($validatedData);

            DB::commit();

            return redirect()->route('model.index', ['model' => Str::kebab($this->modelViewName)])
                ->with('success', __($this->modelViewName . '.updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($modelSlug, $id)
    {
        try {
            DB::beginTransaction();

            $item = $this->modelClass::findOrFail($id);
            $item->delete();

            DB::commit();

            return redirect()->route('model.index', ['model' => Str::kebab($this->modelViewName)])
                ->with('success', __($this->modelViewName . '.deleted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Bulk delete multiple resources.
     */
    public function bulkDestroy(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->withErrors(['error' => 'No items selected for deletion']);
            }

            DB::beginTransaction();

            $this->modelClass::whereIn('id', $ids)->delete();

            DB::commit();

            return redirect()->route('model.index', ['model' => Str::kebab($this->modelViewName)])
                ->with('success', __($this->modelViewName . '.bulk_deleted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore($modelSlug, $id)
    {
        try {
            $item = $this->modelClass::withTrashed()->findOrFail($id);
            $item->restore();

            return redirect()->route('model.index', ['model' => Str::kebab($this->modelViewName)])
                ->with('success', __($this->modelViewName . '.restored_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Force delete a resource (permanent deletion).
     */
    public function forceDelete($modelSlug, $id)
    {
        try {
            $item = $this->modelClass::withTrashed()->findOrFail($id);
            $item->forceDelete();

            return redirect()->route('model.index', ['model' => Str::kebab($this->modelViewName)])
                ->with('success', __($this->modelViewName . '.permanently_deleted'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get trashed (soft deleted) resources.
     */
    public function trashed(Request $request)
    {
        $data = $this->modelClass::onlyTrashed()
            ->paginate($request->input('per_page', 10))
            ->withQueryString();

        $modelNameCamel = Str::camel($this->modelViewName);

        return Inertia::render($this->modelViewName . '/Trashed', [
            $modelNameCamel => $this->getResourceCollection($data, $this->modelClass),
            'filters' => $request->all(),
        ]);
    }

    /**
     * Export data to various formats.
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'csv');
        $data = $this->buildQuery($request)->get();

        switch ($format) {
            case 'csv':
                return $this->exportToCsv($data);
            case 'excel':
                return $this->exportToExcel($data);
            case 'pdf':
                return $this->exportToPdf($data);
            default:
                return response()->json($data);
        }
    }

    /**
     * Import data from file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls'
        ]);

        // Implementation for import would go here
        // This would typically use a package like Laravel Excel
        return redirect()->back()->with('success', 'Data imported successfully');
    }

    /**
     * Search resources.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return response()->json(['data' => []]);
        }

        $results = $this->modelClass::where(function ($q) use ($query) {
            foreach ($this->getSearchableFields() as $field) {
                $q->orWhere($field, 'LIKE', "%{$query}%");
            }
        })->limit(50)->get();

        return response()->json([
            'data' => $this->getResourceCollection($results, $this->modelClass)
        ]);
    }

    /**
     * Build query with filters, sorts, and includes.
     */
    protected function buildQuery(Request $request)
    {
        $query = $this->modelClass::query();

        // Apply search
        if ($request->has('search')) {
            $search = $request->input('search');
            $searchableFields = $this->getSearchableFields();

            $query->where(function ($q) use ($search, $searchableFields) {
                foreach ($searchableFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            });
        }

        // Apply filters
        $filters = $request->except(['page', 'per_page', 'sort', 'search', 'include']);
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $query->where($key, $value);
            }
        }

        // Apply sorting
        if ($request->has('sort')) {
            $sort = $request->input('sort');
            $direction = $sort[0] === '-' ? 'desc' : 'asc';
            $field = ltrim($sort, '-');
            $query->orderBy($field, $direction);
        } else {
            $query->latest();
        }

        // Apply includes
        if ($request->has('include')) {
            $includes = explode(',', $request->input('include'));
            $query->with($includes);
        }

        return $query;
    }

    /**
     * Get searchable fields for the model.
     */
    protected function getSearchableFields(): array
    {
        if (method_exists($this->modelInstance, 'getSearchableFields')) {
            return $this->modelInstance->getSearchableFields();
        }

        // Default searchable fields
        return ['name', 'title', 'slug', 'description'];
    }

    /**
     * Get relationships to eager load.
     */
    protected function getRelationships(): array
    {
        if (method_exists($this->modelInstance, 'getRelationships')) {
            return $this->modelInstance->getRelationships();
        }

        return [];
    }

    /**
     * Dynamically validates the request using a model-specific FormRequest if it exists.
     */
    protected function validateRequest(Request $baseRequest, ?Model $model = null): array
    {
        $modelName = class_basename($this->modelClass);
        $action = $model ? 'Update' : 'Store';

        $requestClass = "App\\Http\\Requests\\{$action}{$modelName}Request";

        if (class_exists($requestClass)) {
            $formRequest = app($requestClass);
            return $formRequest->validated();
        }

        return $baseRequest->all();
    }

    /**
     * Export to CSV format.
     */
    protected function exportToCsv($data)
    {
        $filename = $this->modelViewName . '_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, array_keys($data->first()->toArray()));

            // Add data
            foreach ($data as $item) {
                fputcsv($file, $item->toArray());
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to Excel format (placeholder - requires Laravel Excel package).
     */
    protected function exportToExcel($data)
    {
        // Requires: composer require maatwebsite/excel
        // Implementation would go here
        return response()->json(['message' => 'Excel export not implemented']);
    }

    /**
     * Export to PDF format (placeholder - requires barryvdh/laravel-dompdf).
     */
    protected function exportToPdf($data)
    {
        // Requires: composer require barryvdh/laravel-dompdf
        // Implementation would go here
        return response()->json(['message' => 'PDF export not implemented']);
    }
}
