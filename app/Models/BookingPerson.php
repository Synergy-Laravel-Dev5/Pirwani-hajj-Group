<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPerson extends Model
{
    protected $table = 'booking_persons';

    protected $fillable = [
        'booking_id',
        'full_name',
        'passport_number',
        'cnic',
        'phone',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
