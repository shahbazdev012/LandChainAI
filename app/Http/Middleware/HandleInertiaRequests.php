<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

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
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
                'roles' => $request->user()?->getRoleNames()->values()->all() ?? [],
            ],
            'flash' => [
                'toast' => fn (): ?array => $this->toast($request),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * Translate one-off session flash keys into a single toast payload.
     *
     * @return array{type: string, message: string}|null
     */
    private function toast(Request $request): ?array
    {
        foreach (['success', 'error', 'warning', 'info'] as $type) {
            if ($request->session()->has($type)) {
                return ['type' => $type, 'message' => (string) $request->session()->get($type)];
            }
        }

        return null;
    }
}
