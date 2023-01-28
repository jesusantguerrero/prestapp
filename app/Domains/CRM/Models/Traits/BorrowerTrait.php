<?php

namespace App\Domains\CRM\Models\Traits;

use App\Domains\Loans\Models\Loan;

trait BorrowerTrait {
    public function loans() {
        return $this->hasMany(Loan::class);
    }

    public function hasLateLoans() {
        return $this->loans()->late()->count();
    }

    public function hasActiveLoans() {
        return $this->loans()->active()->count();
    }
}
