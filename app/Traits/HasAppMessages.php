<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

trait HasAppMessages
{
    /**
     * Handle a successful action for a specific model.
     *
     * @param string|object $model The model instance or class name
     * @param string $action The action performed (created, updated, deleted, restored)
     * @param string|null $route The route to redirect to (optional)
     * @param array $options Extra options for vue-toastification (timeout, position, etc.)
     * @return \Illuminate\Http\RedirectResponse
     */
   protected function checkSuccess($model, ?string $action = null, ?string $route = null, array $options = [])
    {
        $modelName = $this->getReadableModelName($model);

        // --- AUTOMATIC DETECTION LOGIC ---
        if (is_null($action)) {
            if ($model instanceof Model && $model->wasRecentlyCreated) {
                $action = 'created';
            } elseif (request()->isMethod('DELETE')) {
                $action = 'deleted';
            } else {
                $action = 'updated';
            }
        }
        $standardActionKey = 'messages.success.' . $action;

        if (Lang::has($standardActionKey)) {
            $message = __($standardActionKey, ['model' => $modelName]);
        } else {
            $message = __($action, ['model' => $modelName]);
        }

        $response = $route ? to_route($route) : back();

        return $response->with('notification', [
            'type' => 'success',
            'title' => __('messages.titles.success'),
            'message' => $message,
            'options' => array_merge([
                'timeout' => 5000,
                'closeOnClick' => true,
                'pauseOnFocusLoss' => true,
                'pauseOnHover' => true,
                'draggable' => false,
                'draggablePercent' => 0.6,
                'showCloseButtonOnHover' => false,
                'hideProgressBar' => false,
                'closeButton' => 'button',
                'icon' => true,
                'rtl' => false,
            ], $options)
        ]);
    }
    /**
     * Handle a specific error manually.
     *
     * @param string $message Custom message or translation key
     * @param \Throwable|null $exception Optional exception for logging/dev output
     */
   protected function checkError(string $message, ?\Throwable $exception = null, array $options = [])
    {
        $devDetails = null;

        if (config('app.debug') && $exception) {
            $devDetails = [
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
            Log::error($exception->getMessage(), $devDetails);
            $options['timeout'] = false; 
        }

        return back()->with('notification', [
            'type' => 'error',
            'title' => __('messages.titles.error'),
            'message' => __($message),
            'dev_details' => $devDetails,
            'options' => array_merge([
                'timeout' => 5000,
                'closeOnClick' => true,
                'pauseOnFocusLoss' => true,
                'pauseOnHover' => true,
                'draggable' => false,
                'draggablePercent' => 0.6,
                'showCloseButtonOnHover' => false,
                'hideProgressBar' => false,
                'closeButton' => 'button',
                'icon' => true,
                'rtl' => false,
            ], $options)
        ]);
    }

    /**
     * Helper to get a human-readable model name.
     * Checks for a 'getModelLabel' method on the model, otherwise uses class basename.
     */
    private function getReadableModelName($model): string
    {
        if (is_object($model)) {
            if (method_exists($model, 'getModelLabel')) {
                return $model->getModelLabel();
            }
            $class = get_class($model);
        } else {
            $class = $model;
        }

        return Str::headline(class_basename($class));
    }
}