<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPersonRoomAllocation extends Model
{
    use HasFactory;

    protected $table = 'booking_person_room_allocations';

    protected $fillable = [
        'booking_id',
        'booking_person_id',
        'location',
        'hotel_name',
        'room_number',
        'room_type',
        'gender',
        'check_in',
        'check_out',
        'notes',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function person()
    {
        return $this->belongsTo(BookingPerson::class, 'booking_person_id');
    }
}
