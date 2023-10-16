<?php

namespace App\Domains\CRM\Models;

use Illuminate\Support\Str;
use Spatie\Searchable\Searchable;
use Illuminate\Support\Facades\DB;
use Spatie\Searchable\SearchResult;
use Database\Factories\ClientFactory;
use App\Domains\CRM\Enums\ClientStatus;
use Illuminate\Database\Eloquent\Model;
use App\Domains\CRM\Models\Traits\OwnerTrait;
use App\Domains\CRM\Models\Traits\TenantTrait;
use App\Domains\CRM\Models\Traits\BorrowerTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model implements Searchable {
    use HasFactory;
    use OwnerTrait;
    use TenantTrait;
    use BorrowerTrait;
    // use Searchable;

    protected $fillable = [
      'user_id',
      'team_id',
      'names',
      'lastnames',
      'display_name',
      'dni',
      'dni_type',
      'email',
      'cellphone',
      'address_details',
      "work_name",
      "work_email",
      "work_cellphone",
      "work_address_details",
      "bank_name",
      "bank_account_number",
      "owner_distribution_date",
      'status',
      'is_lender',
      'is_owner',
      'is_tenant',
    ];
    protected $appends = ['fullName'];

    protected $casts = [
      'generated_distribution_dates' => 'array',
      'status' => ClientStatus::class
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($client) {
            $client->display_name = $client->names . ' ' . $client->lastnames;
        });
    }

     public function getSearchResult(): SearchResult
     {
         return new SearchResult(
            $this,
            $this->display_name,
         );
     }

    public function checkStatus() {
        if($this->hasLateLoans()) {
            $this->updateQuietly([
                'status' => ClientStatus::Late
            ]);
        } else if ( $this->hasActiveLoans()) {
          $this->updateQuietly([
              'status' => ClientStatus::Active
          ]);
        } else if ($this->units()->count()) {
          $this->updateQuietly([
            'status' => ClientStatus::Active
          ]);
        } else if ($this->rents()->count()) {
          $this->updateQuietly([
            'status' => ClientStatus::Active
          ]);
        } else {
          $this->updateQuietly([
            'status' => ClientStatus::Inactive
          ]);
        }
    }

    // stats
    public function outstandingBalance() {
      return $this->invoices()->sum('debt');
    }

    public function deposits() {
      return $this->invoices()->category(PropertyInvoiceTypes::Deposit->name())->sum(DB::raw('total - debt'));
    }

    /**
     * Determine the full name of the client
     */

    protected function fullName(): Attribute {
      return new Attribute(
        get: fn($value, $attributes) => $attributes['names'] . ' ' . $attributes['lastnames']
      );
    }


    public function getFullNameAttribute() {
      return $this->names . ' ' . $this->lastnames;
    }

    public function scopeActive($query)
    {
      return $query->where('status', ClientStatus::Active);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return ClientFactory::new();
    }


    public static function findOrCreateByName($session, string $name) {
        $client = self::where(
          [
              'display_name' => $name,
              'team_id' => $session['team_id'],
          ])->limit(1)->get();

      if (!count($client)) {
          $names = explode(" ", trim($name));
          $lastNames = [...$names];
          array_shift($lastNames);
          return self::create([
              'names' => $names[0],
              'lastnames' => count($lastNames) ? implode(" ", $lastNames) : "",
              'user_id' => $session['user_id'],
              'team_id' => $session['team_id'],
          ]);
      } else {
          return $client->first();
      }
  }
}
