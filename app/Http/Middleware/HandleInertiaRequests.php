<?php

namespace App\Http\Middleware;

use App\Models\Specialty;
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
        $locale = app()->getLocale();
        if (! in_array($locale, ['kz', 'ru', 'en'], true)) {
            $locale = 'ru';
        }

        $uiPath = lang_path($locale.'/ui.php');
        $ui = is_file($uiPath) ? require $uiPath : [];

        return [
            ...parent::share($request),
            'locale' => $locale,
            'ui' => $ui,
            'all_specialties' => fn () => Specialty::where('is_active', true)
                ->select('id', 'title', 'code')
                ->orderBy('title')
                ->get(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
