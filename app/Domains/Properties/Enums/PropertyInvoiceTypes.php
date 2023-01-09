<?php

namespace App\Domains\Properties\Enums;

enum PropertyInvoiceTypes: string {
  case Deposit = 'deposit';
  case Rent = 'rent';
  case Charge = 'charge';
  case Fee = 'fee';
  case LateFee = 'rent_late_fee';
  case UtilityExpense = 'utility';
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
