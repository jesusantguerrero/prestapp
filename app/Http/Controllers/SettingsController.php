<?php

namespace App\Http\Controllers;

use App\Actions\Journal\CreateTeamSettings;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Insane\Journal\Models\Core\Tax;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tabName = "business")
    {

        return Inertia::render('Settings/Index', [
            // "taxesDefinition" => Tax::where('team_id', $request->user()->current_team_id)->get(),
            // "tabName" =>  $tabName,
            // "settingData" => Setting::getBySection($request->user()->current_team_id, $tabName)
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Response $response)
    {
        $settings = $request->post();
        $entryData = [
            'user_id' =>  $request->user()->id,
            'team_id' => $request->user()->current_team_id
        ];

        foreach ($settings as $settingName => $setting) {
            $setting = array_merge($entryData, [
                "value" => $setting,
                "name" => $settingName
            ]);
            $resource = Setting::where([
                'user_id' =>  $request->user()->id,
                'team_id' => $request->user()->current_team_id,
                'name' => $settingName
            ])->limit(1)->get();

            if (count($resource)) {
                $resource[0]->update($setting);
            } else {
                $resource = Setting::create($setting);
            }
        }

        $res = Setting::getFormatted([
            'user_id' =>  $request->user()->id,
            'team_id' => $request->user()->current_team_id
        ]);

        return $response->setContent($res);
    }

    public function setup(Request $request) {
        (new CreateTeamSettings)->create($request->user()->current_team_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $id)
    {
        $businessData = [];
        $teamId = $request->user()->current_team_id;
        if ($id !== 'business') {
            $businessData = Setting::getByTeam($teamId);
        }

        $taxes = Tax::where('team_id', $teamId)->get();
        return Inertia::render("Settings/".ucfirst($id), [
            "taxes" => $taxes,
            "settingData" => Setting::getBySection($teamId, $id),
            "businessData" => $businessData
        ]);
    }
}
