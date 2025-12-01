<?php

namespace App\Http\Middleware;

use App\Services\ComponentService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\App;
use App\Settings\GeneralSettings;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        $componentService = new ComponentService();
        $user = $request->user();
        if ($user) {
            $user->load('roles', 'addresses');
        }
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $user,
                'roles' => $request->user()?->getRoleNames() ?? [],
                'permissions' => $request->user()?->getAllPermissions()->pluck('name'),
                'addresses' => $user?->addresses ?? [],
                'listings_count' => $user?->listings->count() ?? 0,
            ],
            'locale' => fn() => App::getLocale(),
            // 'locale' => $request->user()->locale ?? $request->getPreferredLanguage(),
            'supportedLocales' => fn() => config('app.supported_locales'),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'navigation' => $componentService->getStructuredComponent('main_navigation'),
            'footer' => $componentService->getStructuredComponent('site_footer'),
            'page' => session(
                'page_data',
                [
                    'title' => [
                        'key' => 'app.name',
                        'params' => []
                    ],
                    'description' => [
                        'key' => 'app.description',
                        'params' => [],
                    ],
                    'keywords' => 'app.keywords',
                ]
            ),
            'settings' => function () use ($request) {
                if ($request->routeIs('admin.*')) {
                    return [
                        'general' => app(GeneralSettings::class)->toArray(),
                    ];
                }

                return null;
            },
            'flash' => [
                'notification' => fn () => $request->session()->get('notification')
            ],
        ];
    }
}
