<?php

namespace App\Domains\CRM\Models;

use App\Domains\Loans\Models\Loan;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\Rent;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Insane\Journal\Models\Core\Account;
use Insane\Journal\Models\Invoice\Invoice;

class Client extends Model {
    use HasFactory;

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
      'status'
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

    public function loans() {
        return $this->hasMany(Loan::class);
    }

    public function hasLateLoans() {
        return $this->loans()->late()->count();
    }

    public function hasActiveLoans() {
        return $this->loans()->active()->count();
    }

    // As property owner
    public function properties() {
      return $this->hasMany(Property::class, 'owner_id');
    }

    public function getPropertyInvoices() {
      return Invoice::select('invoices.*')->where([
        'rents.owner_id' => $this->id,
        'invoices.status' => 'paid'
      ])
      ->where('invoiceable_type', Rent::class)
      ->join('rents', 'invoiceable_id', 'rents.id')
      ->get();
    }

    public function account() {
      return $this->hasOne(Account::class);
    }

    // As tenant

    public function rents() {
      return $this->hasMany(Rent::class);
    }

    public function invoices() {
      return $this->hasMany(Invoice::class);
    }

    public function checkStatus() {
        if($this->hasLateLoans()) {
            $this->updateQuietly([
                'status' => self::STATUS_LATE
            ]);
        }
    }

    /**
     * Determine the full name of the client
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */

    protected function fullName(): Attribute {
      return new Attribute(
        get: fn($value, $attributes) => $attributes['names'] . ' ' . $attributes['lastnames']
      );
    }


    public function getFullNameAttribute() {
      return $this->names . ' ' . $this->lastnames;
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
