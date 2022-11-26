<?php

namespace App\Http\Controllers;

use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Services\LoanService;
use App\Http\Requests\LoanStoreRequest;

class LoanController extends Controller
{
    public function index() {
        return inertia('Loans/Index', [
            'data' => Loan::all()
        ]);
    }

    public function create() {
        return inertia('Loans/LoanForm');
    }

    public function store(LoanStoreRequest $loanRequest) {
        $validatedData = $loanRequest->validated();
        
        LoanService::createLoan(array_merge($validatedData, [
            'team_id' => $loanRequest->user()->current_team_id,
            'user_id' => $loanRequest->user()->id
        ]), $loanRequest->get('installments'));
    }
}
