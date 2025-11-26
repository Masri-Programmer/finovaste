<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

trait ApiResponse
{
    /**
     * Handle a generic "Success" response
     */
    protected function successResponse($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Handle a generic "Error" response
     */
    protected function errorResponse(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null
        ], $code);
    }

    /**
     * The Magic Method: Automatically generates messages based on the Model and Action
     *
     * @param mixed $model The model instance (e.g., $user) or class name string
     * @param string $action The action performed (created, updated, deleted)
     * @param mixed $data The data to return (optional)
     */
    protected function modelResponse($model, string $action, $data = null): JsonResponse
    {
        // 1. Get the class name without the namespace (e.g., 'App\Models\User' becomes 'User')
        // If $model is null or not an object, fallback to "Resource"
        $modelName = is_object($model) ? class_basename($model) : (is_string($model) ? $model : 'Resource');

        // 2. Format the action (e.g., 'created' -> 'Created Successfully')
        $message = "{$modelName} " . ucfirst($action) . " Successfully";

        // 3. Determine status code (201 for created, 200 for others)
        $code = ($action === 'created') ? 201 : 200;

        return $this->successResponse($data ?? $model, $message, $code);
    }
}