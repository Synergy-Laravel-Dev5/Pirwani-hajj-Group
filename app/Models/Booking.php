<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'company_id',        // ← ye add karo
        'booking_for',        // ← ye add karo
        'package_type',
        'passport_number',
        'package_year',
        'cnic',
        'no_of_pax',
        'care_of',
        'voucher_number',
        'card_number',
        'phone',
        'emergency_phone',
        'departure_date',
        'departure_flight',
        'departure_time',
        'departure_airline',
        'departure_pnr',
        'arrival_date',
        'arrival_flight',
        'arrival_time',
        'arrival_airline',
        'arrival_pnr',
        'visa_passport',
        'visa_given_name',
        'visa_sur_name',
        'visa_dob',
        'visa_company',
        'visa_send_to',
        'package_name',
        'package_cost',
        'visa_charges',
        'flight_charges',
        'other_charges',
        'total_amount',
        'total_received',
        'balance',
        'status',
        'agreement_signature',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function persons()
    {
        return $this->hasMany(BookingPerson::class);
    }

    public function hotels()
    {
        return $this->hasMany(BookingHotel::class);
    }

    public function transports()
    {
        return $this->hasMany(BookingTransport::class);
    }

    public function visas()
    {
        return $this->hasMany(BookingVisa::class);
    }
}
