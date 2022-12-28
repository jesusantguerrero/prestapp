<?php

namespace App\Domains\Loans\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    use HasFactory;

    protected $fillable = [
      'team_id',
      'user_id',
      'name',
      'description',
      'interest_rates',
      'frequency',
      'late_fee',
      'late_fee_type'
  ];

  protected $casts = [
    'interest_rates' => 'array'
  ];
}
