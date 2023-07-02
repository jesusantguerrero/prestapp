<?php
namespace App\Domains\Atmosphere\DTO;

use Spatie\LaravelData\Data;

class ReportVisualData extends Data {
  public function __construct(
    public mixed $type,
    public mixed $data,
  )
  {

  }
}
