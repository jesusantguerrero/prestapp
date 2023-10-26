<?php

namespace App\Domains\Academic\Http\Controllers;

use Modules\Academic\Entities\ClassRoom;
use App\Http\Controllers\InertiaController;

class ClassroomController extends InertiaController
{
  public function __construct(ClassRoom $classroom)
  {
      $this->model = $classroom;
      $this->searchable  = ['name'];
      $this->templates = [
          "index" => 'Academic/List',
          "create" => 'Academic/Form',
          "edit" => 'Academic/Form',
          "show" => 'Academic/Show'
      ];
      $this->validationRules = [
        'owner_id' => 'numeric',
        'address' => 'string',
        'price' => 'required'
      ];
      $this->sorts = ['created_at'];
      $this->includes = [];
      $this->filters = [];
      $this->page = 1;
      $this->limit = 10;
      $this->resourceName= "classrooms";
  }
}
