<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Querify;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    protected $model;
    protected $createdMessage = "created";
    protected $searchable = ["id"];
    protected $validationRules = [];
    protected $sorts = [];
    protected $includes = [];
    protected $appends = [];
    protected $filters = [];
    use Querify;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $queryParams = $request->query() ?? [];
        $queryParams['limit'] = $queryParams['limit'] ?? 50;

        return $this->getModelQuery($request);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateLocal($request);
        $data = $request->post();
        $data['user_id'] = $request->user()->id;
        $data['team_id'] = $request->user()->current_team_id;
        $resource = $this->model::create($data);
        $this->afterSave($data, $resource);
        return [
            "message" => $this->createdMessage,
            "data" => $resource
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $queryParams = $request->query();
        $relationships = isset($queryParams['relationships']) ? $queryParams['relationships'] : [];
        $query = $this->model::with($relationships)->find($id);

        return $query;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $resource = $this->model::find($id);
        $resource->update($request->post());
        return $resource;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $resource = $this->model::find($id);
        $resource->delete();
        return $resource;
    }

    public function validateLocal(Request $request) {
       return $request->validate($this->validationRules);
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bulkDelete(Request $request)
    {
        $items = $request->post();
        $this->model::whereIn('id', $items)->delete();
        return $items;
    }
}
