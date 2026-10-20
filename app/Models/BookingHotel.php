<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingHotel extends Model
{
    protected $fillable = [
        'booking_id',
        'location',
        'hotel_name',
        'allotment_basis',
        'no_of_nights',
        'check_in',
        'check_out',
        'hijri_check_in',
        'hijri_check_out',
        'room_type',
        'room_number',
        'gender',
        'meal_plan',
        'no_of_rooms',
        'no_of_beds',
        'rate_per_unit',
        'amount',
        'hotel_voucher'
    ];

    protected static function booted()
    {
        static::saving(function ($hotel) {
            if (empty($hotel->check_in)) $hotel->check_in = null;
            if (empty($hotel->check_out)) $hotel->check_out = null;
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
