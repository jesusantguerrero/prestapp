<?php

namespace App\Models;

use App\Domains\CRM\Models\Client;
use App\Domains\Properties\Models\Property;
use App\Domains\Properties\Models\PropertyUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyUnitSnapshot extends Model
{
    protected $fillable = [
        'property_unit_id',
        'property_id',
        'snapshot_date',
        'status',
        'rent_status',
        'tenant_id',
        'monthly_rent',
        'invoice_status',
        'invoice_amount',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'monthly_rent' => 'decimal:2',
        'invoice_amount' => 'decimal:2',
    ];

    public function propertyUnit(): BelongsTo
    {
        return $this->belongsTo(PropertyUnit::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
} 