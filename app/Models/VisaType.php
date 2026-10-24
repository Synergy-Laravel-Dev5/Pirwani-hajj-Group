<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaType extends Model
{
    protected $fillable = [
        'visa_country_id',
        'visa_name',
        'b2b_rate_pkr',
        'visa_fee_usd',
        'process_time',
        'notes',
        'requirements',
    ];

    protected $casts = [
        'b2b_rate_pkr' => 'float',
        'visa_fee_usd' => 'float',
        'requirements' => 'array',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(VisaCountry::class, 'visa_country_id');
    }
}
