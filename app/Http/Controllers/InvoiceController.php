<?php

namespace App\Http\Controllers;

use App\Domains\Properties\Services\OwnerService;
use App\Http\Controllers\Traits\HasEnrichedRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Insane\Journal\Contracts\PdfExporter;
use Insane\Journal\Helpers\CategoryHelper;
use Insane\Journal\Journal;
use Insane\Journal\Models\Core\Category;
use Insane\Journal\Models\Core\Tax;
use Insane\Journal\Models\Invoice\Invoice;
use Insane\Journal\Models\Product\Product;
use Insane\Journal\Services\InvoiceService;
use Exception;

class InvoiceController
{
    use HasEnrichedRequest;

    public function getRequestType() {
        return str_contains(request()->url(), 'bills') ? INVOICE::DOCUMENT_TYPE_BILL : INVOICE::DOCUMENT_TYPE_INVOICE;
    }

    public function getFilterType() {
      $type = $this->getRequestType();
      $filters = request()->get('filter');
      return isset($filters['type']) ? explode('|', $filters['type']) : [$type];
    }

    public function index(Request $request)
    {
        $type = $this->getFilterType();
        $filters = $request->query('filter');

        [$startDate, $endDate] = $this->getFilterDates($filters);

        $invoices = Invoice::where([
          'team_id' => $request->user()->currentTeam->id
      ])
      // ->byClient($clientId)
      ->whereIn('type', $type)
      ->with(['invoiceAccount', 'invoiceAccount.category'])
      ->orderByDesc('date')
      ->orderByDesc('number')
      ->whereBetween('due_date', [$startDate, $endDate])
      ->paginate()
      ->through(function ($invoice) {
        return (object) [
            "id" => $invoice->id,
            "concept" => $invoice->concept,
            "type" => $invoice->type,
            "category" => $invoice->invoiceAccount->category->alias ?? $invoice->invoiceAccount->category->name,
            "account_name" => $invoice->invoiceAccount->alias ?? $invoice->invoiceAccount->name,
            "date" => $invoice->date,
            "client_name" => $invoice->client?->display_name,
            "number" => $invoice->number,
            "series" => $invoice->series,
            "status" => $invoice->status,
            "total" => $invoice->total,
            "debt" => $invoice->debt
        ];
      });

        return inertia(config('journal.invoices_inertia_path') . '/Index', [
            "invoices" => $invoices,
            "total" => $invoices->sum('total'),
            'outstanding' => $invoices->sum('debt'),
            'paid' => $invoices->sum(function ($invoice) {
              return $invoice->total - $invoice->debt;
            }),
            "type" => $type
        ]);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create(Request $request)
    {
        $teamId = $request->user()->current_team_id;
        $type = $this->getRequestType($request);
        $accountCategories =  $type == Invoice::DOCUMENT_TYPE_BILL ? ['expected_payments_vendors', 'credit_card'] : ['cash_and_bank', 'expected_payments_customers'];
        return inertia(config('journal.invoices_inertia_path') . '/Edit', [
            'invoice' => null,
            'type' => $type,
            'products' => Product::where([
                'team_id' => $teamId
            ])->with(['price', 'taxes'])->get(),
            'clients' => Journal::listClientsOf($teamId),
            "categories" => Category::where([
                'depth' => 0
            ])->with([
                'subCategories',
                'subcategories.accounts' => function ($query) use ($teamId) {
                    $query->where('team_id', '=', $teamId);
                },
            ])->get(),
            "accounts" => $teamId ? CategoryHelper::getAccounts($teamId, $accountCategories) : null,
            'availableTaxes' => Tax::where("team_id", $teamId)->get(),
        ]);
    }


    public function store(Request $request, Response $response)
    {
        $postData = $request->post();
        $postData['user_id'] = $request->user()->id;
        $postData['team_id'] = $request->user()->current_team_id;
        Invoice::createDocument($postData);
        return redirect("/invoices");
    }

    private function getInvoiceSecured($invoiceId, $secured = true) {
      $invoice = Invoice::find($invoiceId);
      if ($secured && ($invoice->team_id !== request()->user()->current_team_id || $this->getRequestType() !== $invoice->type)) {
        throw new Exception('This is not allowed');
      }
      return $invoice;
    }

    /**
    * Show the form for editing a resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function show(int $invoiceId)
    {
      try {
        $invoice = $this->getInvoiceSecured($invoiceId);
        $invoiceData = $invoice->getInvoiceData();

        return inertia('Journal/Invoices/Show', [
          'invoice' => $invoiceData,
          'businessData' => Setting::getByTeam($invoice->team_id),
          'type' => $invoice->type,
        ]);
      } catch (Exception $e) {
        redirect('/invoices');
      }
    }

    /**
    * Show the form for editing a resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function edit(int $invoiceId, InvoiceService $invoiceService)
    {
      try {
        $invoice = $this->getInvoiceSecured($invoiceId);
        return inertia(config('journal.invoices_inertia_path') . '/Edit',
          $invoiceService->getEditableData($invoice)
        );
      } catch (Exception $e) {
        return redirect('/invoices');
      }
    }

    public function update(Invoice $invoice, InvoiceService $invoiceService)
    {
        try {
          if ($invoice->team_id != request()->user()->current_team_id) return;
          $invoiceService->update($invoice, request()->post());
          return Redirect("/invoices/$invoice->id/edit");
        } catch (Exception $e) {
          return redirect()->back()->withErrors(['default' => $e->getMessage()]);
        }
    }


    public function print(Invoice $invoice) {
      $exporter = app(PdfExporter::class);
      $exporter->process($invoice);
      return $exporter->previewAs($invoice->concept);
    }

    public function destroy(Request $request, Response $response, $id) {
      $postData = $request->post();
      $postData['user_id'] = $request->user()->id;
      $postData['team_id'] = $request->user()->current_team_id;
      $invoice = Invoice::where([
          'team_id'=> $request->user()->id,
          'id' => $id
      ])->first();
      $invoice->delete();
      if ($request->query('json')) {
          return $response->sendContent($invoice);
      }
      return Redirect()->back();
  }

    /**
    * Show the form for editing a resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function publicPreview(int $invoiceId)
    {
      try {
        $invoice = $this->getInvoiceSecured($invoiceId, false);
        $isJson = request()->query('json');
        $withReport = request()->query('report');

        $report = OwnerService::occupancyReportByMonth($invoice->team_id, $invoice->client_id, $invoice->due_date);
        dd($report, "Hello world");

        $response = [
          'invoice' => $invoice->getInvoiceData(),
          'businessData' => Setting::getByTeam($invoice->team_id),
          'type' => $invoice->type,
          "occupancyReport" => $report
        ];

        if ($isJson) {
          return response($response, 200);
        } else {
          return inertia(config('journal.invoices_inertia_path') . '/Preview', $response);
        }
      } catch (Exception $e) {
        redirect('/invoices');
      }
    }

  /**
   * add payment to invoice.
   * POST invoices/:id/add-payment
   *
   * @param {object} ctx
   * @param {Request} ctx.request
   * @param {Response} ctx.response
   */
    public function addPayment(Request $request, Response $response, $id)
    {
        $invoice = Invoice::find($id);
        $postData = $request->post();

        try {
          $payment = $invoice->createPayment($postData);
          $invoice->save();
          return $response->send($payment);
        } catch (Exception $e) {
            return response([
              'status' => [
                  'message' => $e->getMessage()
              ]
            ], 400);
        }
    }

    public function markAsPaid(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $invoice->markAsPaid();
        return redirect()->back();
    }


    /**
     * delete payment from invoice.
     * POST invoices/:id/add-payment
     *
     * @param {object} ctx
     * @param {Request} ctx.request
     * @param {Response} ctx.response
     */
    public function deletePayment(Response $response, $id, $paymentId)
    {
        $resource = Invoice::find($id);
        $resource->deletePayment($paymentId);
        $resource->save();
        return $response->send($resource);
    }
}
