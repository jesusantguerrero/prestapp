<?php

namespace App\Domains\Atmosphere\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'team_id',
      'name',
      'description',
      'values'
    ];

    protected $casts = [
      "values" => 'array'
    ];
}
