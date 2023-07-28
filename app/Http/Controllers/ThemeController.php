<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domains\Atmosphere\Models\Setting;
use App\Domains\Atmosphere\Services\ApplicationConfigService;
use App\Domains\Atmosphere\Services\ThemeService;
use Exception;
use Inertia\Inertia;
use Insane\Journal\Models\Core\Tax;
use Symfony\Component\HttpFoundation\Response;

class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tabName = "business")
    {

        return Inertia::render('Settings/Index', [
            "taxesDefinition" => Tax::where('team_id', $request->user()->current_team_id)->get(),
            "tabName" =>  $tabName,
            "settingData" => Setting::getBySection($request->user()->current_team_id, $tabName)
        ]);
    }

    public function store(ThemeService $service, ApplicationConfigService $configService)
    {
      try {
        $postData = request()->post();
        $service->store(auth()->user(),$postData);
        $configService->clear(auth()->user());
      } catch (Exception $e) {
        response($e->getMessage(), Response::HTTP_NOT_FOUND);
      }
    }
}
