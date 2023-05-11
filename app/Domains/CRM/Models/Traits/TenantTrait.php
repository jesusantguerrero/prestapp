<?php

namespace App\Domains\CRM\Models\Traits;

use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Core\Category;
use Insane\Journal\Models\Invoice\Invoice;
use PhpParser\Node\Expr\Cast\Double;

trait TenantTrait {
    // As tenant
    public function rents() {
      return $this->hasMany(Rent::class);
    }

    public function rent() {
      return $this->hasOne(Rent::class)->latest('date');
    }

    public function invoices() {
      return $this->hasMany(Invoice::class)->latest('date');
    }

    public function depositBalance(string $uniqueField = "security_deposits") {
      $category = Category::byUniqueField($uniqueField, $this->team_id);
      return abs($category->transactionBalance($this->id)->sum('balance'));
    }
}
