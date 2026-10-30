<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'year',
        'company_id',
        'package_number',
        'category',
        'zone',
        'camp_category',
        'camp_zone',
        'stay_type',
        'stay_duration',
        'departure_date_str',
        'arrival_date_str',
        'departure_sector',
        'arrival_sector',
        'hijri_year',
        'gregorian_year',
        'qurbani_status',
        'qurbani_charges',
        'qurbani_note',
        'package_title',
        'name',
        'code',
        'days',
        'maktab',
        'maktab_number',
        'medina_arrival',
        'hajj_duration',
        'hijri_start_day',
        'hijri_start_month',
        'pkr_roe',
        'usd_roe',
        'gbp_roe',
        'euro_roe',
        'aed_roe',
        'room_type',
        'azizia_room_type',
        'makkah_type',
        'medinah_type',
        'azizia_type',
        'mina_type',
        'makkah_a',
        'makkah_b',
        'madinah_a',
        'madinah_b',
        'giveaway_note',
        'adult_pkr',
        'child_pkr',
        'infant_pkr',
        'adult_sar',
        'child_sar',
        'infant_sar',
        'adult_usd',
        'child_usd',
        'infant_usd',
        'adult_eur',
        'child_eur',
        'infant_eur',
        'adult_gbp',
        'child_gbp',
        'infant_gbp',
        'adult_aed',
        'child_aed',
        'infant_aed',
        'notes',
        'price_disclaimer',
        'services_title',
        'services_content',
        'feature_icons',
        'jeddah_taxi_fare',
        'madinah_taxi_fare',
        'maktab_c_quad_pkr',
        'maktab_c_quad_usd',
        'maktab_c_triple_pkr',
        'maktab_c_triple_usd',
        'maktab_c_double_pkr',
        'maktab_c_double_usd',
        'maktab_a_quad_pkr',
        'maktab_a_quad_usd',
        'maktab_a_triple_pkr',
        'maktab_a_triple_usd',
        'maktab_a_double_pkr',
        'maktab_a_double_usd',
        'azizia_quad_pkr',
        'azizia_quad_usd',
        'azizia_triple_pkr',
        'azizia_triple_usd',
        'azizia_double_pkr',
        'azizia_double_usd',
        'package_included_points',
        'instructions_points',
        'documents_required',
        'important_note',
        // Newly added Umrah & unified package fields
        'package_type',
        'status',
        'package_code',
        'is_group_package',
        'visa_status',
        'is_dual_country',
        'price_sharing',
        'price_quad',
        'price_triple',
        'price_double',
        'duration_pricing_options',
        'key_features',
        'inclusions',
        'exclusions',
        'requirements_text',
        'package_image',
        'valid_from',
        'valid_until',
        'subtitle',
        'additional_destinations',
        // Newly added Domestic & International unified fields
        'departure_city',
        'destination_city',
        'destination_country',
        'is_multi_city',
        'primary_hotel',
        'hotel_star_rating',
        'stay_nights',
        'meal_plan',
        'ground_transfer_fleet',
        'price_per_person',
        'hotel_amenities',
        'description_notes',
        'banner_image',
        // Brochure Schema Additions
        'is_price_on_request',
        'package_excludes_list',
        'itinerary_days',
        'duration_nights',
        'sightseeing_summary',
    ];

    protected $casts = [
        'is_price_on_request'      => 'boolean',
        'package_excludes_list'    => 'array',
        'itinerary_days'           => 'array',
        'duration_nights'          => 'integer',
        'makkah_a'                 => 'array',
        'makkah_b'                 => 'array',
        'madinah_a'                => 'array',
        'madinah_b'                => 'array',
        'feature_icons'            => 'array',
        'package_included_points'  => 'array',
        'instructions_points'      => 'array',
        'is_group_package'         => 'boolean',
        'is_dual_country'          => 'boolean',
        'is_multi_city'            => 'boolean',
        'duration_pricing_options' => 'array',
        'additional_destinations'  => 'array',
        'hotel_amenities'          => 'array',
        'stay_nights'              => 'integer',
        'price_sharing'            => 'float',
        'price_quad'               => 'float',
        'price_triple'             => 'float',
        'price_double'             => 'float',
        'price_per_person'         => 'float',
        'valid_from'               => 'date',
        'valid_until'              => 'date',
    ];

    protected $appends = ['package_image_url', 'banner_image_url'];

    public function getPackageImageUrlAttribute(): ?string
    {
        if (empty($this->package_image)) {
            return null;
        }

        if (str_starts_with($this->package_image, 'http://') || str_starts_with($this->package_image, 'https://')) {
            return $this->package_image;
        }

        return asset('storage/' . ltrim($this->package_image, '/'));
    }

    public function getBannerImageUrlAttribute(): ?string
    {
        if (empty($this->banner_image)) {
            return null;
        }

        if (str_starts_with($this->banner_image, 'http://') || str_starts_with($this->banner_image, 'https://')) {
            return $this->banner_image;
        }

        return asset('storage/' . ltrim($this->banner_image, '/'));
    }

    // Scopes
    public function scopeUmrah($query)
    {
        return $query->where('package_type', 'umrah');
    }

    public function scopeHajj($query)
    {
        return $query->where('package_type', 'hajj');
    }

    public function scopeDomestic($query)
    {
        return $query->where('package_type', 'domestic');
    }

    public function scopeInternational($query)
    {
        return $query->where('package_type', 'international');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Relationships
    public function accommodations()
    {
        return $this->hasMany(PackageAccommodation::class);
    }

    public function itinerary()
    {
        return $this->hasOne(PackageItinerary::class);
    }

    public function terms()
    {
        return $this->hasOne(PackageTerm::class);
    }

    public function maktabAddress()
    {
        return $this->hasOne(PackageMaktabAddress::class);
    }

    public function transports()
    {
        return $this->hasMany(PackageTransport::class);
    }

    public function transportFlights()
    {
        return $this->hasMany(PackageTransportFlight::class);
    }

    public function transportTrains()
    {
        return $this->hasMany(PackageTransportTrain::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function giveaways()
    {
        return $this->belongsToMany(Giveaway::class, 'package_giveaway');
    }

    public function trainingSessions()
    {
        return $this->belongsToMany(TrainingSession::class, 'package_training_session');
    }

    public function hajjApplications()
    {
        return $this->hasMany(HajjApplication::class);
    }

    public function inquiries()
    {
        return $this->hasMany(PackageInquiry::class);
    }
}
