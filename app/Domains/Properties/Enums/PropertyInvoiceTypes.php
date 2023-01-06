<?php

namespace App\Domains\Properties\Enums;

enum PropertyInvoiceTypes {
  case Deposit;
  case Rent;
  case Charge;
  case Fee;
  case UtilityExpense;
  case OwnerDistribution;

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
