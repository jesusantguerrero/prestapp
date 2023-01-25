<?php

namespace App\Http\Controllers\Loans;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;
use App\Domains\Loans\Services\LoanService;
use App\Domains\Loans\Services\LoanTransactionsService;
use App\Http\Controllers\InertiaController;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Insane\Journal\Helpers\ReportHelper;
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
            "create" => 'Loans/LoanForm',
            "edit" => 'Loans/LoanForm',
            "show" => 'Loans/Show'
        ];
        $this->validationRules = [
            'client_id' => 'numeric',
            'amount' => 'numeric',
            'count' => 'numeric',
            'frequency' => 'string',
            'grace_days' => 'numeric',
            'interest_rate' => 'numeric|max:100',
            'installments' => 'array'
        ];
        $this->sorts = ['created_at'];
        $this->includes = ['client'];
        $this->filters = [];
        $this->resourceName= "loans";
    }

    public function __invoke(Request $request) {
      $reportHelper = new ReportHelper();
      $teamId = $request->user()->current_team_id;

      return inertia('Loans/Overview',
      [
          "revenue" => $reportHelper->revenueReport($teamId),
          "activeLoanClients" => ClientService::clientsWithActiveLoans($teamId),
          "loanCapital" => LoanService::disposedCapitalFor($teamId),
          "loanExpectedInterest" => LoanService::expectedInterestFor($teamId),
          "loanPaidInterest" => LoanService::paidInterestFor($teamId),
          'bank' => $reportHelper->smallBoxRevenue('bank', $teamId),
          'dailyBox' => $reportHelper->smallBoxRevenue('daily_box', $teamId),
          'cashOnHand' => $reportHelper->smallBoxRevenue('cash_on_hand', $teamId),
          'nextInvoices' => $reportHelper->nextInvoices($teamId),
          'debtors' => $reportHelper->debtors($teamId),
      ]);
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

    public function updateStatus(Loan $loan) {
      if (request()->user()->current_team_id == $loan->team_id) {
        $loan->updateStatus();
      }
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

    public function markAsPaid(Loan $loan, LoanInstallment $installment) {
        if ($installment->loan_id == $loan->id) {
            $installment->markAsPaid();
        }
    }

    public function numberToWords($number) {
      $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
      return $formatter->format($number) . "\n";
    }

    public function printPaymentDocument(Loan $loan, PaymentDocument $paymentDocument) {
      $teamId = request()->user()->current_team_id;
      $user = request()->user();
      $receipt = $paymentDocument->toArray();
      $receipt['resource_name'] = 'Prestamo';
      $receipt['client_name'] = $loan->client->display_name;
      $receipt['total_in_words'] = $this->numberToWords($paymentDocument->amount);

      $receipt['description'] = $paymentDocument->payments->reduce(function ($description, $payment) {

        return $description . "Pagaré {$payment->payable->number} ";
      });

      return inertia('Prints/Receipt', [
        "company" => Setting::getBySection($teamId, 'business'),
        "receipt" => $receipt,
        "user" => $user
      ]);
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
      $clientId = $filters ? $filters['client'] : null;

      $qInvoice = LoanService::invoices($teamId, $clientId);
      $invoices = LoanService::invoices($teamId, $clientId)->paginate();

      return inertia('Loans/PaymentCenter',
      [
          'invoices' => $invoices,
          'outstanding' => $qInvoice->sum(DB::raw('amount - amount_paid')),
          'paid' => $qInvoice->sum('amount_paid'),
          'clients' => $qInvoice
          ->groupBy('loan_installments.client_id')
          ->select(DB::raw('client_id id, clients.display_name'))
          ->join('clients', 'clients.id', 'loan_installments.client_id')
          ->get(),
          'loans' => $qInvoice->groupBy('loan_id')
          ->select(DB::raw('loan_id id, loans.amount amount, sum(loans.amount - loans.amount_paid) debt'))
          ->join('loans', 'loans.id', 'loan_installments.loan_id')
          ->get()
      ]);
    }
}
