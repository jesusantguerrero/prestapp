<?php

namespace App\Domains\Properties\Models;

use App\Domains\CRM\Models\Client;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Core\Category;

class Property extends Model {

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

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($property) {
            $property->account_id = $property->account_id ?? self::createPayableAccount($property, 'rent');
            $property->owner_account_id = $property->account_owner_id ?? self::createPayableAccount($property, 'expected_payments_owners', $property->owner);
            $property->deposit_account_id = $property->account_deposit_id ?? self::createPayableAccount($property, 'security_deposits');
            $property->commission_account_id = $property->account_owner_id ?? self::createPayableAccount($property, 'expected_commissions_owners');
            $property->name = $property->name ?? $property->shortName;
        });
    }

    public function owner() {
        return $this->belongsTo(Client::class, 'owner_id');
    }

    public function contract() {
      return $this->hasOne(Rent::class)->latestOfMany('created_at');
    }

    public static function createPayableAccount($payable, $parentCategory, $client = null)
    {
        $category = Category::where('display_id', $parentCategory)->first();
        if ($client) {
          $accountName = "Owner {$payable->owner_id} {$payable->owner?->fullName}";
        } else {
          $accountName = "{$category->number}-{$payable->shortName}";
        }

        $accounts = Account::firstOrCreate([
          'display_id' =>  Str::slug($accountName, '_'),
          "category_id" => $category->id,
          'team_id' => $payable->team_id,
          'user_id' => $payable->user_id
        ], [
          "client_id" => $payable->owner_id,
          "currency_code" => "DOP",
          "name" => $accountName
        ]);

        return $accounts->id;
    }

    protected function shortName(): Attribute {
      return new Attribute(
        get: fn($value, $attributes) => explode(',', $attributes['address'])[0]
      );
    }
}
