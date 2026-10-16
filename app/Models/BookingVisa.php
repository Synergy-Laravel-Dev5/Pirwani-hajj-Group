<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingVisa extends Model
{
    protected $table = 'booking_visas';

    protected $fillable = [
        'booking_id',
        'passport_number',
        'given_name',
        'date_of_birth',
        'company',
        'send_to',
        'status',
    ];

    protected static function booted()
    {
        static::saving(function ($visa) {
            if (empty($visa->date_of_birth)) $visa->date_of_birth = null;
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
