<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPerson extends Model
{
    protected $table = 'booking_persons';

    protected $fillable = [
        'booking_id',
        'hajj_id',
        'hb_number',
        'full_name',
        'gender',
        'surname',
        'given_name',
        'dob',
        'passport_number',
        'date_of_issue',
        'passport_issue_date',
        'passport_expiry_date',
        'cnic',
        'phone',
        'photo',
        'room_number',
        'room_type',
        'hotel_name',
        'location',
        'room_gender',
    ];

    protected static function booted()
    {
        static::creating(function ($person) {
            if (empty($person->hb_number)) {
                $person->hb_number = self::generateNextHbNumber();
            }
        });
    }

    /**
     * Auto-generate sequential HB number (HB000001, HB000002, ...)
     */
    public static function generateNextHbNumber(): string
    {
        $last = self::whereNotNull('hb_number')
            ->where('hb_number', '!=', '')
            ->orderBy('id', 'desc')
            ->first();

        $next = 1;
        if ($last && !empty($last->hb_number)) {
            preg_match('/\d+/', $last->hb_number, $matches);
            if (!empty($matches[0])) {
                $next = ((int) $matches[0]) + 1;
            } else {
                $next = (int) $last->id + 1;
            }
        } else {
            $next = self::count() + 1;
        }

        return 'HB' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
