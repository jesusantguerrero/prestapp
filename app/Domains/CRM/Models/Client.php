<?php

namespace App\Domains\CRM\Models;

use App\Domains\CRM\Models\Traits\BorrowerTrait;
use App\Domains\CRM\Models\Traits\OwnerTrait;
use App\Domains\CRM\Models\Traits\TenantTrait;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Client extends Model {
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

    const STATUS_INACTIVE = 'INACTIVE';
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_LATE =  'LATE';
    const STATUS_SUSPENDED = 'SUSPENDED';

    protected $casts = [
      'generated_distribution_dates' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($client) {
            $client->display_name = $client->names . ' ' . $client->lastnames;
        });
    }

    public function checkStatus() {
        if($this->hasLateLoans()) {
            $this->updateQuietly([
                'status' => self::STATUS_LATE
            ]);
        } else if ( $this->hasActiveLoans()) {
          $this->updateQuietly([
              'status' => self::STATUS_ACTIVE
          ]);
        } else if ($this->units()->count()) {
          $this->updateQuietly([
            'status' => self::STATUS_ACTIVE
          ]);
        } else if ($this->rents()->count()) {
          $this->updateQuietly([
            'status' => self::STATUS_ACTIVE
          ]);
        } else {
          $this->updateQuietly([
            'status' => self::STATUS_INACTIVE
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
      return $query->where('status', Client::STATUS_ACTIVE);
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
}
