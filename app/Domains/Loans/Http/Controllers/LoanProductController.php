<?php

namespace App\Domains\Loans\Http\Controllers;

use App\Domains\Loans\Models\LoanProduct;
use App\Http\Controllers\InertiaController;

class LoanProductController extends InertiaController
{
  public function __construct(LoanProduct $loanProduct)
  {
      $this->model = $loanProduct;
      $this->searchable = ['name'];
      $this->templates = [
          "index" => 'Loans/LoanProductsIndex',
          "create" => 'Loans/LoanProductForm',
      ];
      $this->validationRules = [
          'name' => 'string',
          'description' => 'string',
          'interest_rates' => 'array',
          'frequency' => 'string',
          'late_fee' => 'numeric',
          'late_fee_type' => 'string',
          'grace_days' => 'numeric',
      ];
      $this->sorts = ['name'];
      $this->filters = [];
      $this->resourceName= "loanProducts";
  }
}
