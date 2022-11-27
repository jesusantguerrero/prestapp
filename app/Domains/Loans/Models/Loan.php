<?php 

namespace App\Domains\Loans\Models;

use App\Domains\CRM\Models\Client;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model {
    protected $fillable = [
        'team_id', 
        'user_id', 
        'contact_id', 
        'amount',
        'interest_rate',
        'start_date'
    ];
    public function contact() {
        return $this->belongsTo(Client::class, 'contact_id');
    }
    
    public function installments() {
        return $this->hasMany(LoanInstallment::class);
    }
}