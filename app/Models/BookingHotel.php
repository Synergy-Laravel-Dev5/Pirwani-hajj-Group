<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingHotel extends Model
{
    protected $fillable = [
        'booking_id',
        'location',
        'hotel_name',
        'no_of_nights',
        'check_in',
        'check_out',
        'room_type',
        'room_number',
        'gender',
        'no_of_rooms',
        'hotel_voucher'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
