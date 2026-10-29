<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaCountry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_name',
        'country_code',
        'monument_picture',
        'country_flag',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['monument_picture_url', 'country_flag_url'];

    public function visaTypes(): HasMany
    {
        return $this->hasMany(VisaType::class, 'visa_country_id');
    }

    public function getMonumentPictureUrlAttribute(): ?string
    {
        return $this->monument_picture ? asset('storage/' . $this->monument_picture) : null;
    }

    public function getCountryFlagUrlAttribute(): ?string
    {
        return $this->country_flag ? asset('storage/' . $this->country_flag) : null;
    }
}
