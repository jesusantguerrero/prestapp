<?php
namespace App\Domains\Atmosphere\DTO;

use Spatie\LaravelData\Data;

class ReportData extends Data {
  public function __construct(
    public mixed $title,
    public mixed $description,
    public mixed $startDate,
    public mixed $endDate,
    public string $type,
    public ReportVisualData $content,
    public mixed $footNotes,
    public string $footNote,
  )
  {

  }
}
