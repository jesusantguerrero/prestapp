<?php

namespace App\Domains\Properties\Models;

use App\Domains\CRM\Models\Client;
use Illuminate\Database\Eloquent\Model;

class Property extends Model {

    const STATUS_BUILDING = 'BUILDING';
    const STATUS_AVAILABLE =  'AVAILABLE';
    const STATUS_RENTED = 'RENTED';
    const STATUS_MAINTENANCE = 'MAINTENANCE';

    protected $fillable = [
        'team_id',
        'user_id',
        'owner_id',
        'address',
    ];

    // protected
    protected $creditCategory = 'loan_line_credit';
    protected $creditAccount = 'Customer Demand Deposits';

    public function owner() {
        return $this->belongsTo(Client::class, 'owner_id');
    }

    public function activeContract() {
      return $this->hasOne(Rent::class)->latestOfMany('created_at');
  }
}
