<?php
namespace App\Domains\Accounting\DTO;

use Spatie\LaravelData\Data;

class ReceiptData extends Data {
  public function __construct(
    public mixed $business,
    public mixed $client,
    public mixed $description,
    public mixed $lines,
    public string $total,
    public mixed $footNotes,
    public string $footNote,
  )
  {

  }
}
