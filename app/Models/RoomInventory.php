<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class RoomInventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hotel_room_inventories';

    protected $guarded = ['id'];

    protected $casts = [
        'check_in'    => 'date',
        'check_out'   => 'date',
        'total_rooms' => 'integer',
        'male_beds'   => 'integer',
        'female_beds' => 'integer',
        'total_beds'  => 'integer',
        'cost_rate'   => 'decimal:2',
        'selling_rate'=> 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Company::class, 'supplier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get room type summary breakdown (Double, Triple, Quad, Quint, Single, Sharing, Suite)
     */
    public static function getHotelSummary($hotelId = null, $checkIn = null, $checkOut = null)
    {
        $types = ['Double', 'Triple', 'Quad', 'Quint', 'Single', 'Sharing', 'Suite'];
        $summary = [];

        foreach ($types as $type) {
            $invQuery = self::active()->whereRaw('LOWER(TRIM(room_type)) = ?', [strtolower($type)]);
            if ($hotelId) {
                $invQuery->where('hotel_id', $hotelId);
            }

            if ($checkIn && $checkOut) {
                $cIn = date('Y-m-d', strtotime($checkIn));
                $cOut = date('Y-m-d', strtotime($checkOut));
                $invQuery->where(function ($q) use ($cIn, $cOut) {
                    $q->where(function ($sub) {
                        $sub->whereNull('check_in')->whereNull('check_out');
                    })->orWhere(function ($sub) use ($cIn, $cOut) {
                        $sub->where('check_in', '<=', $cOut)->where('check_out', '>=', $cIn);
                    });
                });
            }

            $totalStock = (int) $invQuery->sum('total_rooms');

            // Booked query from BookingHotel
            $bookedQuery = BookingHotel::whereHas('booking', function ($q) {
                $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            })->whereRaw('LOWER(TRIM(room_type)) = ?', [strtolower($type)]);

            if ($hotelId) {
                $hotel = Hotel::find($hotelId);
                if ($hotel) {
                    $bookedQuery->where('hotel_name', 'like', "%{$hotel->name}%");
                }
            }

            $booked = (int) $bookedQuery->sum('no_of_rooms');
            $available = max(0, $totalStock - $booked);
            $pct = $totalStock > 0 ? min(100, round(($booked / $totalStock) * 100)) : 0;

            $summaryData = [
                'type'        => $type,
                'total_stock' => $totalStock,
                'booked'      => $booked,
                'available'   => $available,
                'percentage'  => $pct,
            ];

            if (strtolower($type) === 'sharing') {
                $maleStock = (int) (clone $invQuery)->sum('male_beds');
                $femaleStock = (int) (clone $invQuery)->sum('female_beds');

                $hasMaleBeds = Schema::hasColumn('booking_hotels', 'male_beds');
                $hasFemaleBeds = Schema::hasColumn('booking_hotels', 'female_beds');

                $maleBooked = $hasMaleBeds ? (int) (clone $bookedQuery)->sum('male_beds') : 0;
                $femaleBooked = $hasFemaleBeds ? (int) (clone $bookedQuery)->sum('female_beds') : 0;

                $maleAvail = max(0, $maleStock - $maleBooked);
                $femaleAvail = max(0, $femaleStock - $femaleBooked);

                $summaryData['male_stock'] = $maleStock;
                $summaryData['male_booked'] = $maleBooked;
                $summaryData['male_available'] = $maleAvail;
                $summaryData['female_stock'] = $femaleStock;
                $summaryData['female_booked'] = $femaleBooked;
                $summaryData['female_available'] = $femaleAvail;
            }

            $summary[$type] = $summaryData;
        }

        return $summary;
    }
}
