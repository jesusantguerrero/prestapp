<?php 

namespace App\Domains\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model {
    protected $fillable = ['names', 'lastnames', 'display_name', 'dni', 'dni_type', 'email', 'cellphone', 'address_details'];
}