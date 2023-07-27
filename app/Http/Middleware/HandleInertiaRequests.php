<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Services\ApplicationConfigService;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\Category;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $team = $user ? $user->currentTeam : null;
        $applicationConfigService = new  ApplicationConfigService();

        return array_merge(parent::share($request), [
            "accounts" => $team ? Account::getByDetailTypes($team->id) : [],
            "categories" => $team ? Category::where([
                'categories.team_id' => $team->id,
                'categories.resource_type' => 'transactions'
            ])
                ->whereNull('parent_id')
                ->orderBy('index')
                ->with('subCategories')
                ->get() : [''],
            'unreadNotifications' => function() use ($user) {
              return [
                "count" => $user ? $user->unreadNotifications->count() : 0,
                "data" => $user ? $user->unreadNotifications : []
              ];
            },
            "application" => $applicationConfigService->once($team, $user)
        ]);
    }
}
