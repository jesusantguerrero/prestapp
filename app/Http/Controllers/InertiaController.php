<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasEnrichedRequest;
use App\Http\Traits\Querify;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class InertiaController extends Controller {
    use Querify;
    use HasEnrichedRequest;

    protected $model;
    protected $templates;
    protected $searchable = ["id"];
    protected $validationRules = [];
    protected $sorts = [];
    protected $includes = [];
    protected $appends = [];
    protected $filters = [];
    protected $page;
    protected $limit;
    protected $service;
    protected $responseType = "inertia";
    protected $resourceName;

    protected function index(Request $request) {
        $resourceName = $this->resourceName ?? $this->model->getTable();
        $resources = $this->parser($this->getModelQuery($request));

        return Inertia::render($this->templates['index'],
        array_merge([
            $resourceName => $resources,
            "serverSearchOptions" => $this->getServerParams(),
            "kpis" => $this->service?->getListKpi($request->user()->current_team_id)
        ], $this->getIndexProps($request, $resources)));
    }

    public function create(Request $request) {
        return Inertia::render($this->templates['create'], []);
    }

    public function edit(Request $request, int $id) {
        $resource = $this->getModelQuery($request, $id)[0];

        return Inertia::render($this->templates['edit'], array_merge(
          [$this->model->getTable() => $resource],
          $this->getEditProps($request, $resource)));
    }

    public function show(Request $request, int $id) {
        $resource = $this->getModelQuery($request, $id)[0];
        if (!$resource) {
          return to_route($this->model->getTable());
        }
        return Inertia::render($this->templates['show'],  array_merge(
          [$this->model->getTable() => $resource],
          $this->getEditProps($request, $resource)));
    }

    public function store(Request $request, Response $response) {
        $postData = $request->post();
        $postData['user_id'] = $request->user()->id;
        $postData['team_id'] = $request->user()->current_team_id;
        $isJson = $request->query('json');

        $this->validate($request, $this->getValidationRules($postData));
        try {
          $resource = $this->createResource($request, $postData);

          $this->afterSave($postData, $resource);
          if ($this->responseType == 'inertia' && !$isJson) {
              return redirect()->back();
          } else {
              return $response->setContent([
                'status' => 'success',
                'data' => $resource
              ]);
          }
        } catch (Exception $e) {
          if ($this->responseType == 'inertia' && !$isJson) {
            return redirect()->back()->withErrors($e->getMessage());
          } else {
            return $response->setContent([
              'status' => 'error',
              'error' => $e->getMessage(),
            ])->setStatusCode(400);
          }
        }
    }

    protected function createResource(Request $request, $postData) {
        return $this->model::create($postData);
    }

    public function update(Request $request, int $id) {
        $resource = $this->model::findOrFail($id);
        $postData = $request->post();
        $resource = $this->updateResource($request, $resource, $postData);
        $this->afterSave($postData, $resource);

        if ($this->responseType == 'inertia') {
            return Redirect::back();
        } else {
            return $resource;
        }
    }

    protected function updateResource(Request $request, $resource, $postData) {
      $resource->update($postData);
      return $resource;
    }

    public function destroy(Request $request, int $id) {
        $resource = $this->model::findOrFail($id);
        try {
          if ($this->validateDelete($request, $resource)) {
              $resource->delete();
              return Redirect::back();
          }
          return Redirect::back()->withErrors(['default' => 'You cannot delete this resource']);
        } catch (Exception $e) {
          return Redirect::back()->withErrors(['default' => $e->getMessage()]);
        }
    }

    protected function getIndexProps(Request $request, Collection|ResourceCollection|Array $resources): array {
        return [];
    }

    protected function parser($results) {
        return $results;
    }

    protected function getEditProps(Request $request, $id) {
      return [ ];
    }

    protected function afterSave($postData, $resource): void {

    }

    protected function validateDelete(Request $request, $resource) {
        return true;
    }

    protected function getValidationRules($postData) {
        return $this->validationRules;
    }

    protected function getServerParams() {
      return $this->serverParams;
    }
}
