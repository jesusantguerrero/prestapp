<?php

namespace App\Http\Controllers;

use App\Domains\CRM\Models\Client;
use App\Domains\Loans\Models\Loan;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\Rent;
use Illuminate\Routing\Controller as BaseController;

class SearchController extends BaseController {

    public function index() {
      $searchText = request()->query('search');
      
      return [
        "clients" => Client::search($searchText)->get(),
        "loans" => Loan::search($searchText)->get(),
        "properties" => Property::search($searchText)->get(),
        "rents" => Rent::search($searchText)->get(),
      ];
  }
}

