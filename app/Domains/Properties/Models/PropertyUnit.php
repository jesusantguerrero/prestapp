<?php

namespace App\Domains\Properties\Models;

use App\Domains\CRM\Models\Client;
use Database\Factories\PropertyUnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Insane\Journal\Traits\HasResourceAccounts;
use Laravel\Scout\Searchable;

class PropertyUnit extends Model {
    use HasFactory;
    use HasResourceAccounts;
    use Searchable;

    const STATUS_BUILDING = 'BUILDING';
    const STATUS_AVAILABLE =  'AVAILABLE';
    const STATUS_RENTED = 'RENTED';
    const STATUS_MAINTENANCE = 'MAINTENANCE';

    protected $fillable = [
        'team_id',
        'user_id',
        'owner_id',
        'property_id',
        'name',
        'description',
        'price',
        'status'
    ];

    // protected
    protected $creditCategory = 'expected_payments_vendors';
    protected $creditAccount = 'Customer Demand Deposits';

    public function owner() {
      return $this->belongsTo(Client::class, 'owner_id');
    }

    public function property() {
      return $this->belongsTo(Property::class);
    }

    public function contract() {
      return $this->hasOne(Rent::class, 'unit_id')->latestOfMany('created_at');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return PropertyUnitFactory::new();
    }
}
