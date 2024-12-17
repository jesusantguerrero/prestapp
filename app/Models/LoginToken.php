<?php

namespace App\Models;

use App\Domains\CRM\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginToken extends Model
{
    use HasFactory;


    protected $guarded = [];
    protected $casts = [
      'expires_at' => "datetime",
      // 'consumed_at' => "date",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function isValid()
    {
        return !$this->isExpired() && !$this->isConsumed();
    }

    public function isExpired()
    {
        return $this->expires_at->isBefore(now());
    }

    public function isConsumed()
    {
        return $this->consumed_at !== null;
    }

    public function consume()
    {
        $this->consumed_at = now();
        $this->save();
    }
}
