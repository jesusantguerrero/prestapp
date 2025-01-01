<?php

namespace App\Domains\CRM\Enums;

enum ClientStatus: string {
  case Inactive = 'INACTIVE';
  case Active = 'ACTIVE';
  case Late =  'LATE';
  const Suspended = 'SUSPENDED';
}
