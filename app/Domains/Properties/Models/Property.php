<?php

namespace App\Domains\Properties\Models;

use Laravel\Scout\Searchable;
use App\Domains\CRM\Models\Client;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Model;
use Insane\Journal\Models\Invoice\Invoice;
use Insane\Journal\Traits\HasResourceAccounts;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Domains\Properties\Enums\PropertyInvoiceTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model {
    use HasFactory;
    use HasResourceAccounts;
    // use Searchable;

    const STATUS_BUILDING = 'BUILDING';
    const STATUS_AVAILABLE =  'AVAILABLE';
    const STATUS_RENTED = 'RENTED';
    const STATUS_MAINTENANCE = 'MAINTENANCE';
    const STATUS_ADMINISTRATION_FINISHED = 'ADMINISTRATION_FINISHED';

    protected $fillable = [
        'team_id',
        'user_id',
        'owner_id',
        'account_id',
        'address',
        'name',
        'description',
        'price',
        'property_type',
        'status'
    ];

    // protected
    protected $creditCategory = 'expected_payments_vendors';
    protected $creditAccount = 'Customer Demand Deposits';
    protected $appends = ['short_name', 'balance', 'available_units', 'unit_count'];

    protected static function boot() {
        parent::boot();
        static::saving(function ($property) {
            $property->setResourceAccount('account_id', 'rent');
            $property->setResourceAccount('owner_account_id', 'expected_payments_owners', $property->owner);
            $property->setResourceAccount('deposit_account_id', 'security_deposits');
            $property->setResourceAccount('commission_account_id', 'expected_commissions_owners');
            $property->setResourceAccount('late_fee_account_id', 'owed_commissions');
            $property->name = $property->name ?? $property->shortName;
        });

        static::deleting(function ($property) {
            $property->units()->delete();
        });
    }

    public function initAccounts() {
      $this->setResourceAccount('account_id', 'rent');
      $this->setResourceAccount('owner_account_id', 'expected_payments_owners', $this->owner);
      $this->setResourceAccount('deposit_account_id', 'security_deposits');
      $this->setResourceAccount('commission_account_id', 'expected_commissions_owners');
      $this->setResourceAccount('late_fee_account_id', 'owed_commissions');
      $this->name = $this->name ?? $this->shortName;
      $this->saveQuietly();
    }

    public function owner() {
        return $this->belongsTo(Client::class, 'owner_id');
    }

    public function units() {
      return $this->hasMany(PropertyUnit::class);
    }

    public function rents() {
      return $this->hasMany(Rent::class);
    }
    public function activeRents() {
      return $this->hasMany(Rent::class)->active();
    }

    public function invoices() {
      return $this->hasManyThrough(Invoice::class, Rent::class, 'id', 'invoiceable_id')
      ->where('invoiceable_type', Rent::class);
    }

    public function expenses() {
      return $this->morphMany(Invoice::class, 'invoiceable')
      ->where('invoiceable_type', Property::class)
      ->where('category_type', PropertyInvoiceTypes::UtilityExpense);
    }

    public function close() {
      $this->update([
        'status' => Property::STATUS_ADMINISTRATION_FINISHED
      ]);
    }

    public function allInvoices() {
      return [...$this->invoices->toArray(), ...$this->expenses->toArray()];
    }

    protected function shortName(): Attribute {
      return new Attribute(
        get: function($value, $attributes) {
          if(isset($attributes['address'])) {
            return explode(',', $attributes['address'])[0];
          }
          return '';
        }
      );
    }

    protected function getBalanceAttribute() {
      return $this->invoices->sum('debt');
    }

    protected function getUnitCountAttribute() {
      return $this->units->count();
    }

    protected function getAvailableUnitsAttribute() {
      return $this->units()->where('status', PropertyUnit::STATUS_AVAILABLE)->count();
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return PropertyFactory::new();
    }
}
