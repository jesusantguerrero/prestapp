<?php 

namespace App\Domains\Loans\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model {
    protected $fillable = [
        'team_id', 
        'user_id', 
        'contact_id', 
        'amount',
        'start_date'
    ];

    public function installments() {
        return $this->hasMany(LoanInstallment::class);
    }
}