<?php

namespace App\Domains\Properties\Models;

use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class RentSearch extends Rent implements Searchable {
  protected $table = 'rents';

  public function getSearchResult(): SearchResult
  {

    $url = route('rents.show', ["rent" => $this]);

    return new SearchResult(
        $this,
        "{$this->owner_name}({$this->address}) -> $this->client_name",
        $url
    );
  }
}
