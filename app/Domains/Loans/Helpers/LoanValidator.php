<?php

namespace App\Domains\Loans\Helpers;

use Exception;
use Insane\Journal\Models\Core\Account;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class LoanValidator {

    public function __construct(
      private mixed $formData
    ) { }

    public function isValid() {
      $account = Account::find($this->formData['source_account_id']);
      if ($account->balance < $this->formData['amount']) {
        throw new Exception(__('Source account balance (:balance) is lower than loan amount (:amount), please fund your account :accountName', [
          'accountName' => $account->alias ?? $account->label,
          'balance' => $account->balance,
          'amount' => $this->formData['amount'],
        ]));
      }
      return true;
    }
}
