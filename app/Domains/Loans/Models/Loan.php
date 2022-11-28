<?php 

namespace App\Domains\Loans\Models;

use App\Domains\CRM\Models\Client;
use Insane\Journal\Models\Core\Transaction;
use Insane\Journal\Traits\Transactionable;
class Loan extends Transactionable {

    protected $fillable = [
        'team_id', 
        'user_id', 
        'client_id', 
        'amount',
        'interest_rate',
        'start_date'
    ];

    // protected 
    protected $creditCategory = 'loan_line_credit';
    protected $creditAccount = 'Customer Demand Deposits';

    public function client() {
        return $this->belongsTo(Client::class, 'client_id');
    }
    
    public function installments() {
        return $this->hasMany(LoanInstallment::class);
    }

    public function getTransactionItems() {
        return [];
    }

    public static function getCategoryName($payable): string {
        return "expected_payments_customers";
    }

    public function getTransactionDescription() {
        return "Desembolso de prestamo #code";
    }

    public function getTransactionDirection() {
        return Transaction::DIRECTION_CREDIT;
    }

    public function getAccountId() {
        return $this->account_id;
    }

    public function getCounterAccountId() {
        return $this->client_account_id;
    }
}