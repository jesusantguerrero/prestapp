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
    // use Searchable;

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
        'index',
        'description',
        'price',
        'area',
        'bedrooms',
        'bathrooms',
        'amenities',
        'status'
    ];

    protected $appends = ['clientName'];
    protected $width = ['contract', 'contract.client'];

    protected static function boot() {
      parent::boot();
      static::saving(function ($unit) {
          $index = $unit->property->units()->count() + 1;
          $unit->name = $unit->name ?? 'Unidad ' . $index;
          $unit->team_id = $unit->property->team_id;
          $unit->user_id = $unit->property->user_id;
          $unit->owner_id = $unit->property->owner_id;
          $unit->property_name = $unit->property->name;
          $unit->owner_name = $unit->property->owner_name;
          $unit->index = $index;
      });
  }

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

    public function getClientNameAttribute() {
      return $this->contract?->client->display_name;
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
