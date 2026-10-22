<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonDocument extends Model
{
    protected $guarded = ['id'];

    public function person()
    {
        return $this->belongsTo(BookingPerson::class, 'person_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
