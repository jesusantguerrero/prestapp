<?php

namespace App\Domains\CRM\Models\Traits;

use App\Domains\Properties\Models\Rent;
use Insane\Journal\Models\Invoice\Invoice;

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
}
