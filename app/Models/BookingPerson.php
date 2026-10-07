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
                $person->hb_number = self::generateNextHbNumber($person->booking_id);
            } else {
                $person->hb_number = self::formatHbNumber($person->hb_number);
            }
        });

        static::updating(function ($person) {
            if (!empty($person->hb_number)) {
                $person->hb_number = self::formatHbNumber($person->hb_number);
            }
        });
    }

    /**
     * Standardize HB number to strict 4-digit format: HB0001, HB0002, ..., HB0010
     */
    public static function formatHbNumber(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        $trimmed = trim($value);
        if (preg_match('/(\d+)/', $trimmed, $matches)) {
            $num = (int) $matches[1];
            return 'HB' . str_pad($num, 4, '0', STR_PAD_LEFT);
        }
        return $trimmed;
    }

    /**
     * Mutator to ensure hb_number is always saved in standard HB0001 format
     */
    public function setHbNumberAttribute($value)
    {
        $this->attributes['hb_number'] = self::formatHbNumber($value);
    }

    /**
     * Auto-generate sequential HB number (HB0001, HB0002, ...)
     * If booking_id is provided and that booking already has an HB number, inherits it.
     */
    public static function generateNextHbNumber(?int $bookingId = null): string
    {
        if ($bookingId) {
            $existing = self::where('booking_id', $bookingId)
                ->whereNotNull('hb_number')
                ->where('hb_number', '!=', '')
                ->first();
            if ($existing && !empty($existing->hb_number)) {
                return self::formatHbNumber($existing->hb_number);
            }
        }

        $maxNum = 0;
        $all = self::whereNotNull('hb_number')
            ->where('hb_number', '!=', '')
            ->pluck('hb_number');

        foreach ($all as $hb) {
            if (preg_match('/(\d+)/', $hb, $matches)) {
                $val = (int) $matches[1];
                if ($val > $maxNum) {
                    $maxNum = $val;
                }
            }
        }

        $next = $maxNum > 0 ? ($maxNum + 1) : 1;

        return 'HB' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
