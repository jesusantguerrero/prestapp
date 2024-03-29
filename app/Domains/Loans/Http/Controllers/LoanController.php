<?php

namespace App\Domains\Loans\Http\Controllers;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Services\LoanService;
use App\Domains\Loans\Services\LoanTransactionsService;
use App\Http\Controllers\InertiaController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\PaymentDocument;

class LoanController extends InertiaController
{
    public function __construct(Loan $loan)
    {
        $this->model = $loan;
        $this->searchable = ['client_name', 'amount', 'total', 'repayment'];
        $this->templates = [
            "index" => 'Loans/Index',
            "create" => 'Loans/EditForm',
            "edit" => 'Loans/EditForm',
            "show" => 'Loans/Show'
        ];
        $this->validationRules = [
            'client_id' => 'numeric',
            'amount' => 'numeric',
            'count' => 'numeric',
            'frequency' => 'string',
            'grace_days' => 'numeric',
            'interest_rate' => 'numeric|max:100',
            'installments' => 'array',
            'source_account_id' => 'required|numeric'
        ];
        $this->sorts = ['created_at'];
        $this->includes = ['client'];
        $this->filters = [];
        $this->resourceName= "loans";
    }

    protected function createResource(Request $request, $postData)
    {
        return LoanService::createLoan($postData, $request->get('installments'));
    }

    public function create(Request $request) {
        $teamId = $request->user()->current_team_id;

        return inertia($this->templates['create'], [
            'loans' => null,
            'clients' => ClientService::ofTeam($teamId),
        ]);
    }

    public function refinance(Loan $loan) {
      $stats = LoanService::getStats($loan);
      return inertia('Loans/Refinance', [
          'loans' => array_merge(
          $loan->toArray(),
          [
            'repayment_count' => 0,
            'amount' => $stats->outstandingPrincipal,
            'sourceAccount' => $loan->sourceAccount,
            'client' => $loan->client,
            'installments' => [],
            'paymentDocuments' => $loan->paymentDocuments,
            "stats" => $stats,
            "source_account_id" => $loan->client_account_id,
            "sourceAccount" => Account::find($loan->client_account_id)
          ]),
      ]);
    }

    protected function getEditProps(Request $request, $loan)
    {
      return [
        'loans' => array_merge(
        $loan->toArray(),
        [
          'sourceAccount' => $loan->sourceAccount,
          'client' => $loan->client,
          'installments' => $loan->installments,
          'paymentDocuments' => $loan->paymentDocuments
        ]),
        "stats" => LoanService::getStats($loan)
      ];
    }

    public function update(Request $request, int $id) {
      $loan = Loan::findOrFail($id);
      $loanData = $request->post();
      LoanService::updateLoan($loanData, $loan);
    }

    protected function validateDelete(Request $request, $resource) {
      if ($resource->paymentDocuments()->count()) {
        throw new Exception(__("This loan already has payments and can't be deleted", [], 'es'));
      }
      return true;
    }

    //  options
    public function updateStatus(Loan $loan) {
      if (request()->user()->current_team_id == $loan->team_id) {
        $loan->updateStatus();
      }
    }

    public function close(Loan $loan) {
      $postData = $this->getPostData();
      LoanTransactionsService::close($loan, $postData);
    }

    // payable document related
    public function pay(Loan $loan) {
      $postData = $this->getPostData();
      LoanTransactionsService::pay($loan, $postData);
    }

    public function payoff(Loan $loan) {
      $postData = $this->getPostData();
      LoanTransactionsService::payoff($loan, $postData);
    }


    public function printPaymentDocument(Loan $loan, PaymentDocument $paymentDocument) {
      $receipt = LoanService::getReceipt($loan, $paymentDocument);
      $template = request()->query('type') ?? 'BareTicket';

      return inertia("Prints/$template", [
        "receipt" => $receipt,
        "user" => $paymentDocument->user,
      ]);
    }

    public function deletePaymentDocument(Loan $loan, PaymentDocument $paymentDocument) {
      $this->authorize('deletePayment', $loan);

      LoanService::deletePaymentDocument($loan, $paymentDocument);
    }

    // tabs
    public function getSection(Loan $loan, string $section) {
      $resourceName = $this->resourceName ?? $this->model->getTable();
      $resource = $loan->toArray();
      $sectionName = ucfirst($section);

      return inertia("Loans/$sectionName",
      [
          $resourceName => array_merge($resource, $this->$section($loan)),
          "currentTab" => $section,
          "stats" => LoanService::getStats($loan)
      ]);
    }

    public function agreements(Loan $loan) {
      return [
        "agreements" => $loan->agreements,
        "client" => $loan->client,
      ];
    }

    public function payments(Loan $loan) {
      return [
        "payment_documents" => $loan->paymentDocuments,
        "client" => $loan->client,
      ];
    }


    public function loanSourceAccounts($accountId = null) {
      if ($accountId) {
        return Account::find($accountId);
      }
      return Account::getByCategories(request()->user()->current_team_id, ['cash_and_bank'], $accountId);
    }


    // Payment Center
    public function paymentCenter(Request $request) {
      $teamId = $request->user()->current_team_id;
      $filters = $request->query('filters');
      $currentTab = $request->query('tab') ?? 'pending';
      $clientId = $filters ? $filters['client'] : null;

      $qInvoice = LoanService::paymentReport($teamId, $clientId, $currentTab);
      $invoices = $qInvoice->paginate();

      return inertia('Loans/PaymentCenter',
      [
          'invoices' => $invoices,
          // 'outstanding' => $qInvoice->sum(DB::raw('amount - amount_paid')),
          // 'paid' => $qInvoice->sum('amount_paid'),
          'clients' => $currentTab == 'payments' ? [] : $qInvoice
          ->groupBy('loan_installments.client_id')
          ->select(DB::raw('client_id id, clients.display_name'))
          ->join('clients', 'clients.id', 'loan_installments.client_id')
          ->get(),
          'loans' => $currentTab == 'payments' ? [] : $qInvoice->groupBy('loan_id')
          ->select(DB::raw('loan_id id, loans.amount amount, sum(loans.amount - loans.amount_paid) debt'))
          ->join('loans', 'loans.id', 'loan_installments.loan_id')
          ->get(),
          "currentTab" => $currentTab
      ]);
    }
}
