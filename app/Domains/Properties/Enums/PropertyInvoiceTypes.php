<?php

namespace App\Domains\Properties\Enums;

enum PropertyInvoiceTypes: string {
  // income
  case Rent = 'rent';
  case Charge = 'charge';
  case Fee = 'fee';
  case LateFee = 'rent_late_fee';
  case Deposit = 'deposit';
  case OwnerContribution = 'owner_contribution';
  // Expense
  case UtilityExpense = 'utility';
  case DepositRefund = 'deposit_refund';
  case DepositApply = 'deposit_apply';
  case OwnerDistribution = 'owner_distribution';

  public function color() {

  }

  public function name(): string {
    return match($this) {
      PropertyInvoiceTypes::Deposit => 'deposit',
      PropertyInvoiceTypes::Rent => 'rent',
      PropertyInvoiceTypes::Charge => 'charge',
      PropertyInvoiceTypes::OwnerDistribution => 'owner_distribution',
    };
  }
}
