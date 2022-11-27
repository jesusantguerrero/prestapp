<?php 

namespace App\Domains\Loans\Models;

use Insane\Journal\Traits\IPayableDocument;
use Insane\Journal\Traits\HasPayments;

class LoanInstallment extends HasPayments implements IPayableDocument {
    const STATUS_PENDING = 'PENDING'; 
    const STATUS_LATE =  'LATE'; 
    const STATUS_PAID = 'PAID';
    const STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
    const STATUS_GRACE = 'GRACE';

    protected $statusField = 'payment_status'; 

    protected $fillable = [
        'team_id', 
        'user_id', 
        'loan_id', 
        'installment_number', 
        'due_date', 
        'days',
        'amount',
        'principal',
        'interest',
        'initial_balance',
        'final_balance'
    ];

    public static function calculateTotal($payable) {
        return 0;
    }

    public static function checkStatus($payable) {
        return self::STATUS_PENDING;
    }

    public function getConceptLine(): string {
        return "";
    }
}