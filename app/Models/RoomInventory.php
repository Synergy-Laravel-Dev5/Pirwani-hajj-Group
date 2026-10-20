<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\HotelSaleItem;
use App\Models\BookingHotel;
use App\Models\Hotel;

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
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get room type summary breakdown (Double, Triple, Quad, Quint, Single, Sharing, Bed, Suite)
     */
    public static function getHotelSummary($hotelId = null, $checkIn = null, $checkOut = null)
    {
        $types = ['Double', 'Triple', 'Quad', 'Quint', 'Single', 'Sharing', 'Bed', 'Suite'];
        $summary = [];

        foreach ($types as $type) {
            $isBedType = (strtolower($type) === 'bed');
            $invQuery = self::active()->where(function ($q) use ($type, $isBedType) {
                if ($isBedType) {
                    $q->whereRaw('LOWER(TRIM(room_type)) = ?', ['bed'])
                      ->orWhere('allotment_basis', 'bed');
                } else {
                    $q->whereRaw('LOWER(TRIM(room_type)) = ?', [strtolower($type)])
                      ->where(function ($sub) {
                          $sub->whereNull('allotment_basis')->orWhere('allotment_basis', '!=', 'bed');
                      });
                }
            });

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

            $totalStock = $isBedType 
                ? (int) (clone $invQuery)->sum(DB::raw('GREATEST(total_beds, total_rooms)'))
                : (int) (clone $invQuery)->sum('total_rooms');

            // Booked query STRICTLY from HotelSaleItem (Hotel Sale Invoices to Agents / Clients)
            $saleItemQuery = HotelSaleItem::whereHas('hotelSale', function ($q) {
                $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            });

            if ($hotelId) {
                $hotel = Hotel::find($hotelId);
                if ($hotel) {
                    $saleItemQuery->where(function ($q) use ($hotelId, $hotel) {
                        $q->where('hotel_id', $hotelId)
                          ->orWhere('hotel_name', 'like', "%{$hotel->name}%");
                    });
                }
            }

            if ($isBedType) {
                $saleItemQuery->where(function ($q) {
                    $q->where('allotment_basis', 'bed')
                      ->orWhereRaw('LOWER(TRIM(room_type)) = ?', ['bed']);
                });
            } else {
                $saleItemQuery->whereRaw('LOWER(TRIM(room_type)) = ?', [strtolower($type)])
                    ->where(function ($q) {
                        $q->whereNull('allotment_basis')->orWhere('allotment_basis', '!=', 'bed');
                    });
            }

            $booked = (int) $saleItemQuery->sum('quantity');

            $available = max(0, $totalStock - $booked);
            $pct = $totalStock > 0 ? min(100, round(($booked / $totalStock) * 100)) : 0;

            $summaryData = [
                'type'        => $type,
                'total_stock' => $totalStock,
                'booked'      => $booked,
                'available'   => $available,
                'percentage'  => $pct,
                'is_bed'      => $isBedType,
            ];

            if (strtolower($type) === 'sharing' || $isBedType) {
                $maleStock = (int) (clone $invQuery)->sum('male_beds');
                $femaleStock = (int) (clone $invQuery)->sum('female_beds');

                $maleBooked = (int) (clone $saleItemQuery)->sum('male_beds');
                $femaleBooked = (int) (clone $saleItemQuery)->sum('female_beds');

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
