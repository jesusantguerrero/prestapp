<?php

namespace App\Domains\Loans\Helpers;

class MetaLine {
  public function __construct(
    public string $value, 
    public string $type = "text"
  ) {}
}