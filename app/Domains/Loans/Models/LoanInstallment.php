<?php 

namespace App\Domains\Loans\Models;

use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model {
    const STATUS_PENDING = 'PENDING'; 
    const STATUS_LATE =  'LATE'; 
    const STATUS_PAID = 'PAID';
    const STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
    const STATUS_GRACE = 'GRACE';

    protected $fillable = [
        'team_id', 
        'user_id', 
        'loan_id', 
        'installment_number', 
        'due_date', 
        'amount',
        'principal',
        'interest',
        'initial_balance',
        'final_balance'
    ];
}