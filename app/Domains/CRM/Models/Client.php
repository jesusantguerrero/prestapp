<?php

namespace App\Domains\CRM\Models;

use App\Domains\Loans\Models\Loan;
use Illuminate\Database\Eloquent\Model;

class Client extends Model {
    protected $fillable = ['user_id', 'team_id', 'names', 'lastnames', 'display_name', 'dni', 'dni_type', 'email', 'cellphone', 'address_details', 'status'];

    const STATUS_INACTIVE = 'INACTIVE';
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_LATE =  'LATE';
    const STATUS_SUSPENDED = 'SUSPENDED';

    public function loans() {
        return $this->hasMany(Loan::class);
    }

    public function hasLateLoans() {
        return $this->loans()->late()->count();
    }

    public function hasActiveLoans() {
        return $this->loans()->active()->count();
    }

    public function checkStatus() {
        if($this->hasLateLoans()) {
            $this->updateQuietly([
                'status' => self::STATUS_LATE
            ]);
        }
    }
}
