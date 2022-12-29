<?php

namespace App\Domains\Loans\Jobs;

use App\Domains\Loans\Models\Loan;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Insane\Journal\Models\Core\Transaction;

class CreateLoanTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $loan;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
        $this->formData = [];
    }

        /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->formData['team_id'] = $this->loan->team_id;
        $this->formData['user_id'] = $this->loan->user_id;
        $this->formData['resource_id'] = $this->loan->id;
        $this->formData["transactionable_id"] = $this->loan->id;
        $this->formData['transactionable_type'] = Loan::class;
        $this->formData['date'] =  $this->formData['date'] ?? $this->loan->date ?? date('Y-m-d');
        $this->formData["description"] = $this->formData["description"] ?? "Prestamo a " . $this->loan->client->fullName;
        $this->formData["concept"] = $this->formData["description"] ?? "Prestamo a " . $this->loan->client->fullName;
        $this->formData["direction"] = Transaction::DIRECTION_CREDIT;
        $this->formData["total"] =  $this->formData["total"] ?? $this->loan->total;
        $this->formData["account_id"] = $this->formData['account_id'] ?? $this->loan->source_account_id;
        $this->formData["category_id"] = $this->loan->account_id;
        $this->formData["status"] = "verified";

        if ($transaction = $this->loan->transaction) {
            $transaction->update($this->formData);
        } else {
            $transaction = $this->loan->transaction()->create($this->formData);
        }
        $transaction->createLines($this->getTransactionItems());
        return $transaction;
    }

    protected function getTransactionItems()
    {
        $items = [];

        $items[] = [
            "index" => 0,
            "account_id" => $this->loan->source_account_id,
            "category_id" => null,
            "type" => -1,
            "concept" => $this->formData['concept'],
            "amount" => $this->loan->amount,
            "anchor" => true,
        ];

        $items[] = [
            "index" => 1,
            "account_id" => $this->loan->fees_account_id,
            "category_id" => null,
            "type" => -1,
            "concept" => $this->formData['concept'],
            "amount" => $this->loan->total - $this->loan->amount,
            "anchor" => false,
        ];

        $items[] = [
          "index" => 1,
          "account_id" => $this->loan->client_account_id,
          "category_id" => null,
          "type" => 1,
          "concept" => $this->formData['concept'],
          "amount" => $this->loan->total,
          "anchor" => false,
        ];

        return $items;
    }
}
