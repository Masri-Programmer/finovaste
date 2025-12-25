<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLanguageMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Routing\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',

        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            app('router')->middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);
$middleware->validateCsrfTokens(except: [
            'stripe/webhook', 
        ]);
        $middleware->web(append: [
            SetLanguageMiddleware::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            // \App\Http\Middleware\CspMiddleware::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'developer' => \App\Http\Middleware\IsDeveloper::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
                $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->inertia()) {
                // Try to determine what type of resource wasn't found
                $resourceType = 'Resource';
                
                // Check if this is a listing route
                if (str_contains($request->path(), 'listings/')) {
                    $resourceType = 'Listing';
                }
                
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => 'Not Found',
                    'message' => __('messages.errors.not_found', ['model' => $resourceType]),
                ]);
            }
        });

        // Handle General Exceptions
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->inertia() && !$e instanceof \Illuminate\Validation\ValidationException) {
                
                $isDebug = config('app.debug');
                
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => $isDebug ? get_class($e) : __('messages.titles.error'),
                    'message' => $isDebug ? $e->getMessage() : __('messages.errors.generic_user'),
                    'dev_details' => $isDebug ? [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => collect($e->getTrace())->take(3)->toArray()
                    ] : null,
                    'duration' => 0
                ]);
            }
        });
    })->create();
