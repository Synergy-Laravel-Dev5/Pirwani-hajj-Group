<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'makkah_a' => 'array',
        'makkah_b' => 'array',
        'madinah_a' => 'array',
        'madinah_b' => 'array',
        'feature_icons' => 'array',
    ];

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
    // Flight Details
    public function transportFlights()
    {
        return $this->hasMany(PackageTransportFlight::class);
    }

    // Train Details
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
}
