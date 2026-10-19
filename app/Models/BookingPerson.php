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
        'father_name',
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
        'ticket_status',
        'passport_image',
        'room_number',
        'room_type',
        'hotel_name',
        'location',
        'room_gender',
    ];

    protected static function booted()
    {
        static::creating(function ($person) {
            // If booking_id is provided, check if an HB number already exists for that booking
            if (!empty($person->booking_id)) {
                $existing = self::where('booking_id', $person->booking_id)
                    ->whereNotNull('hb_number')
                    ->where('hb_number', '!=', '')
                    ->value('hb_number');
                if (!empty($existing)) {
                    $person->hb_number = self::formatHbNumber($existing);
                }
            }

            if (empty($person->hb_number)) {
                $person->hb_number = self::generateNextHbNumber($person->booking_id);
            } else {
                $person->hb_number = self::formatHbNumber($person->hb_number);
            }

            // Auto-generate Hajj ID (PW27001, PW27002, ...) if empty
            if (empty($person->hajj_id)) {
                $person->hajj_id = self::generateNextHajjId();
            } else {
                $person->hajj_id = self::formatHajjId($person->hajj_id);
            }
        });

        static::saving(function ($person) {
            if (empty($person->dob)) $person->dob = null;
            if (empty($person->date_of_issue)) $person->date_of_issue = null;
            if (empty($person->passport_issue_date)) $person->passport_issue_date = null;
            if (empty($person->passport_expiry_date)) $person->passport_expiry_date = null;
        });

        static::updating(function ($person) {
            if (!empty($person->hb_number)) {
                $person->hb_number = self::formatHbNumber($person->hb_number);
            }
            if (!empty($person->hajj_id)) {
                $person->hajj_id = self::formatHajjId($person->hajj_id);
            }
        });
    }

    /**
     * Standardize Hajj ID to strict format: PW27001, PW27002, ...
     */
    public static function formatHajjId(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        $trimmed = strtoupper(trim($value));
        if (preg_match('/^(?:PW)?(?:27)?(\d+)$/i', $trimmed, $matches)) {
            $num = (int) $matches[1];
            return 'PW27' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }
        return $trimmed;
    }

    /**
     * Mutator to ensure hajj_id is always saved in standard format
     */
    public function setHajjIdAttribute($value)
    {
        $this->attributes['hajj_id'] = self::formatHajjId($value);
    }

    /**
     * Auto-generate sequential Hajj ID (PW27001, PW27002, ...)
     */
    public static function generateNextHajjId(): string
    {
        $maxNum = 0;
        $all = self::whereNotNull('hajj_id')
            ->where('hajj_id', '!=', '')
            ->whereHas('booking')
            ->pluck('hajj_id');

        foreach ($all as $hid) {
            if (preg_match('/PW27(\d+)/i', $hid, $matches)) {
                $val = (int) $matches[1];
                if ($val > $maxNum) {
                    $maxNum = $val;
                }
            }
        }

        $next = $maxNum > 0 ? ($maxNum + 1) : 1;

        return 'PW27' . str_pad($next, 3, '0', STR_PAD_LEFT);
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
            ->whereHas('booking')
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

    public function roomAllocations()
    {
        return $this->hasMany(BookingPersonRoomAllocation::class, 'booking_person_id');
    }
}
