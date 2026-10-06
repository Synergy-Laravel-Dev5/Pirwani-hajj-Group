<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelRoomCapacity extends Model
{
    use HasFactory;

    protected $table = 'hotel_room_capacities';

    protected $fillable = [
        'hotel_id',
        'hotel_name',
        'location',
        'room_number',
        'room_type',
        'gender',
        'bed_capacity',
        'extra_beds',
        'notes',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Standard capacity map for room types if not found in database.
     */
    public static function standardTypeCapacity(string $type = null): int
    {
        $normalized = strtolower(trim((string) $type));

        if (empty($normalized)) {
            return 2;
        }

        // Check if defined in room_types table
        $roomType = RoomType::whereRaw('LOWER(TRIM(name)) = ?', [$normalized])
            ->orWhereRaw('LOWER(TRIM(code)) = ?', [$normalized])
            ->first();

        if ($roomType && $roomType->capacity > 0) {
            return (int) $roomType->capacity;
        }

        return match ($normalized) {
            'single'            => 1,
            'double'            => 2,
            'triple'            => 3,
            'quad'              => 4,
            'quint'             => 5,
            'sharing'           => 4,
            'suite'             => 2,
            'executive_suite'   => 2,
            'family_room'       => 4,
            'studio'            => 2,
            default             => 2,
        };
    }

    /**
     * Resolve effective bed capacity for a room in a hotel.
     * Looks up custom capacity in hotel_room_capacities; if absent, uses room_type standard capacity.
     */
    public static function resolveCapacity(?string $hotelName, ?string $roomNumber, ?string $roomType = null): int
    {
        $cleanHotel = trim((string) $hotelName);
        $cleanRoom = trim((string) $roomNumber);

        if (!empty($cleanHotel) && !empty($cleanRoom)) {
            $custom = self::whereRaw('LOWER(TRIM(hotel_name)) = ?', [strtolower($cleanHotel)])
                ->whereRaw('LOWER(TRIM(room_number)) = ?', [strtolower($cleanRoom)])
                ->first();

            if ($custom && $custom->bed_capacity > 0) {
                return (int) $custom->bed_capacity;
            }
        }

        return self::standardTypeCapacity($roomType);
    }

    /**
     * Get detailed occupancy info for a specific hotel & room number.
     * Calculates occupied beds from active bookings, available beds, full/overbooked flags, and occupants list.
     */
    public static function getRoomOccupancy(?string $hotelName, ?string $roomNumber, ?string $roomType = null, $checkIn = null, $checkOut = null, $excludeBookingId = null): array
    {
        $cleanHotel = trim((string) $hotelName);
        $cleanRoom = trim((string) $roomNumber);

        $capacity = self::resolveCapacity($cleanHotel, $cleanRoom, $roomType);

        if (empty($cleanHotel) || empty($cleanRoom)) {
            return [
                'capacity'         => $capacity,
                'occupied_beds'    => 0,
                'available_beds'   => $capacity,
                'is_full'          => false,
                'is_overbooked'    => false,
                'occupants_count'  => 0,
                'occupants'        => [],
                'booking_count'    => 0,
            ];
        }

        // Query active BookingHotel records
        $query = BookingHotel::with([
            'booking.client',
            'booking.company',
            'booking.persons',
        ])->whereHas('booking', function ($q) use ($excludeBookingId) {
            $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            if ($excludeBookingId) {
                $q->where('id', '!=', $excludeBookingId);
            }
        })->whereRaw('LOWER(TRIM(room_number)) = ?', [strtolower($cleanRoom)])
          ->whereRaw('LOWER(TRIM(hotel_name)) = ?', [strtolower($cleanHotel)]);

        // Check date overlap if dates provided
        if (!empty($checkIn) && !empty($checkOut)) {
            $cIn = date('Y-m-d', strtotime($checkIn));
            $cOut = date('Y-m-d', strtotime($checkOut));
            $query->where(function ($q) use ($cIn, $cOut) {
                $q->where(function ($sub) {
                    $sub->whereNull('check_in')->whereNull('check_out');
                })->orWhere(function ($sub) use ($cIn, $cOut) {
                    $sub->where('check_in', '<=', $cOut)->where('check_out', '>=', $cIn);
                });
            });
        }

        $bookingHotels = $query->get();

        $occupiedBeds = 0;
        $occupants = [];
        $seenBookingIds = [];

        foreach ($bookingHotels as $bh) {
            $b = $bh->booking;
            if (!$b) continue;

            $seenBookingIds[$b->id] = true;

            $persons = $b->persons;
            if ($persons && $persons->count() > 0) {
                foreach ($persons as $p) {
                    $occupiedBeds++;
                    $occupants[] = [
                        'person_id'      => $p->id,
                        'name'           => $p->full_name ?: trim(($p->given_name ?? '') . ' ' . ($p->surname ?? 'Pilgrim')),
                        'passport'       => $p->passport_number,
                        'cnic'           => $p->cnic,
                        'phone'          => $p->phone ?: $b->phone,
                        'booking_id'     => $b->id,
                        'booking_number' => $b->booking_number ?? ('#BK-' . $b->id),
                        'client_name'    => $b->client->name ?? ($b->company->company_name ?? 'N/A'),
                        'check_in'       => $bh->check_in ? date('d M Y', strtotime($bh->check_in)) : '—',
                        'check_out'      => $bh->check_out ? date('d M Y', strtotime($bh->check_out)) : '—',
                        'room_type'      => $bh->room_type,
                    ];
                }
            } else {
                $pax = max(1, (int) ($b->no_of_pax ?: 1));
                $occupiedBeds += $pax;
                for ($i = 1; $i <= $pax; $i++) {
                    $occupants[] = [
                        'person_id'      => null,
                        'name'           => ($b->client->name ?? 'Guest') . " (Pax #{$i})",
                        'passport'       => $b->passport_number,
                        'cnic'           => $b->cnic,
                        'phone'          => $b->phone,
                        'booking_id'     => $b->id,
                        'booking_number' => $b->booking_number ?? ('#BK-' . $b->id),
                        'client_name'    => $b->client->name ?? ($b->company->company_name ?? 'N/A'),
                        'check_in'       => $bh->check_in ? date('d M Y', strtotime($bh->check_in)) : '—',
                        'check_out'      => $bh->check_out ? date('d M Y', strtotime($bh->check_out)) : '—',
                        'room_type'      => $bh->room_type,
                    ];
                }
            }
        }

        $availableBeds = max(0, $capacity - $occupiedBeds);
        $isFull = ($occupiedBeds >= $capacity && $capacity > 0);
        $isOverbooked = ($occupiedBeds > $capacity);

        return [
            'capacity'         => $capacity,
            'occupied_beds'    => $occupiedBeds,
            'available_beds'   => $availableBeds,
            'is_full'          => $isFull,
            'is_overbooked'    => $isOverbooked,
            'occupants_count'  => count($occupants),
            'occupants'        => $occupants,
            'booking_count'    => count($seenBookingIds),
        ];
    }
}
