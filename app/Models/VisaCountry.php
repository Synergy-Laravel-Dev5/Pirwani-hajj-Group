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
        if (!$this->monument_picture) {
            return null;
        }

        $path = ltrim($this->monument_picture, '/');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/' . $path);
    }

    public function getCountryFlagUrlAttribute(): ?string
    {
        if ($this->country_flag) {
            $path = ltrim($this->country_flag, '/');
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }
            return asset('storage/' . $path);
        }

        // Automatic fallback to FlagCDN if country code is a 2-letter ISO code
        $code = strtolower(trim($this->country_code ?? ''));
        if (strlen($code) === 2 && ctype_alpha($code)) {
            return "https://flagcdn.com/w160/{$code}.png";
        }

        return null;
    }
}
