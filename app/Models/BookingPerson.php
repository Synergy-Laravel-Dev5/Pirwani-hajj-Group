<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPerson extends Model
{
    protected $table = 'booking_persons';

    protected $fillable = [
        'booking_id',
        'full_name',
        'surname',
        'given_name',
        'dob',
        'passport_number',
        'passport_expiry_date',
        'cnic',
        'phone',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
