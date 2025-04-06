<?php

namespace App\Domains\Regimen\Enums;

enum ClientStatus: string {
  case Inactive = 'INACTIVE';
  case Active = 'ACTIVE';
  case Late =  'LATE';
  const Suspended = 'SUSPENDED';
}
