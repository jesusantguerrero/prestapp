<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Services\ThemeService;
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function section(Request $request, $name = "business")
    {
        $businessData = [];
        $teamId = $request->user()->current_team_id;
        if ($name !== 'business') {
            $businessData = Setting::getByTeam($teamId);
        }

        $taxes = Tax::where('team_id', $teamId)->get();
        return Inertia::render("Settings/".ucfirst($name), [
            "taxes" => $taxes,
            "settingData" => Setting::getBySection($teamId, $name),
            "businessData" => $businessData
        ]);
    }

    public function store(ThemeService $service)
    {
      try {
        $postData = request()->post();
        $resource =$service->store(auth()->user(),$postData);
      } catch (Exception $e) {
        dd($e->getMessage());
        response($e->getMessage(), Response::HTTP_NOT_FOUND);
      }
    }
}
