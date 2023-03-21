<?php
namespace App\Domains\Atmosphere\Widgets;

use Spatie\LaravelData\Data;

class StatWidget extends Data {
  public function __construct(
    public string $label,
    public string $icon,
    public int $value
  )
  {

  }
}
