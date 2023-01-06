<?php

namespace App\Domains\Properties\Models;

use App\Domains\CRM\Models\Client;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Traits\HasResourceAccounts;

class Property extends Model {
    use HasFactory;
    use HasResourceAccounts;

    const STATUS_BUILDING = 'BUILDING';
    const STATUS_AVAILABLE =  'AVAILABLE';
    const STATUS_RENTED = 'RENTED';
    const STATUS_MAINTENANCE = 'MAINTENANCE';

    protected $fillable = [
        'team_id',
        'user_id',
        'owner_id',
        'account_id',
        'address',
        'description',
        'price',
        'property_type',
        'status'
    ];

    // protected
    protected $creditCategory = 'expected_payments_vendors';
    protected $creditAccount = 'Customer Demand Deposits';
    protected $appends = ['short_name'];

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
    }

    public function owner() {
        return $this->belongsTo(Client::class, 'owner_id');
    }

    public function units() {
      return $this->hasMany(PropertyUnit::class);
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
