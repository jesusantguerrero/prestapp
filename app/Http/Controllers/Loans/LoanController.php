<?php

namespace App\Http\Controllers\Loans;

use App\Domains\CRM\Services\ClientService;
use App\Domains\Loans\Models\Loan;
use App\Domains\Loans\Models\LoanInstallment;
use App\Domains\Loans\Services\LoanService;
use App\Http\Controllers\InertiaController;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Insane\Journal\Helpers\ReportHelper;
use Insane\Journal\Models\Core\PaymentDocument;

class LoanController extends InertiaController
{
    public function __construct(Loan $loan)
    {
        $this->model = $loan;
        $this->searchable = ['name'];
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

    protected function getEditProps(Request $request, $id)
    {
        $teamId = $request->user()->current_team_id;

        return [
            'loans' => Loan::where('id', $id)->with(['client', 'installments', 'paymentDocuments'])->first(),
            'clients' => ClientService::ofTeam($teamId),
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

    // payable document related
    public function pay(Loan $loan, Request $request) {
      $postData = $this->getPostData($request);

      $loan->createPayment(array_merge($postData, [
          "client_id" => $loan->client_id,
          "documents" => array_map(function ($document) {
            $document['payable_id'] = $document['id'];
            $document['payable_type'] = LoanInstallment::class;
            $document['amount'] = $document['payment'];
            return $document;
          }, $postData['documents'])
      ]));
      $loan->client->checkStatus();
    }

    public function payInstallment(Loan $loan, LoanInstallment $installment, Request $request) {
        $postData = $this->getPostData($request);
        if ($installment->loan_id == $loan->id) {
            $loan->createPayment(array_merge($postData, [
                "client_id" => $loan->client_id,
                "documents" => [[
                    "payable_id" => $installment->id,
                    "payable_type" => LoanInstallment::class,
                    "amount" => $postData['amount'],
                    "amount_due" =>$installment->total - $postData['amount'],
                    "amount_paid" => $postData['amount']
                ]]
            ]));
            $loan->client->checkStatus();
        }
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

        return $description . "Pagaré {$payment->payable->installment_number} ";
      });

      return inertia('Prints/Receipt', [
        "company" => Setting::getBySection($teamId, 'business'),
        "receipt" => $receipt,
        "user" => $user
      ]);
    }

    // Payment Center
}
