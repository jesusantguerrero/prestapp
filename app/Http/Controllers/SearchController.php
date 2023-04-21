<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\RentSearch;
use Illuminate\Routing\Controller as BaseController;
use Spatie\Searchable\Search;

class SearchController extends BaseController {

    public function index() {
      $searchText = request()->query('search');

      return (new Search())
      ->registerModel(Client::class, ['names', 'lastnames'])
      ->registerModel(RentSearch::class, ['owner_name', 'client_name', 'address'])
      ->search($searchText)
      ->groupBy('type');
  }
}

