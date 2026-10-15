<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingHotel;
use App\Models\BookingPerson;
use App\Models\Hotel;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MaktabReportController extends Controller
{
    public function index(Request $request)
    {
        $maktabFilter = $request->get('maktab', 'all');
        $genderFilter = $request->get('gender', 'all');

        // Query BookingHotel records with active bookings
        $query = BookingHotel::with(['booking.client', 'booking.company', 'booking.package', 'booking.persons'])
            ->whereHas('booking', function ($q) {
                $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            });

        // 1. Maktab / Camp Category filter
        if (!empty($maktabFilter) && $maktabFilter !== 'all') {
            $query->whereHas('booking', function ($bq) use ($maktabFilter) {
                if ($maktabFilter === 'maktab_a') {
                    $bq->where(function ($sub) {
                        $sub->where('camp', 'LIKE', '%Maktab A%')
                            ->orWhere('camp', 'LIKE', '%Zone 1%')
                            ->orWhere('camp', 'LIKE', '%Zone 2%')
                            ->orWhereHas('package', function ($pq) {
                                $pq->where('camp_category', 'LIKE', '%A%')
                                   ->orWhere('maktab', 'LIKE', '%A%')
                                   ->orWhere('camp_zone', 'LIKE', '%ZONE 1%');
                            });
                    });
                } elseif ($maktabFilter === 'maktab_c') {
                    $bq->where(function ($sub) {
                        $sub->where('camp', 'LIKE', '%Maktab C%')
                            ->orWhere('camp', 'LIKE', '%Zone 5%')
                            ->orWhereHas('package', function ($pq) {
                                $pq->where('camp_category', 'LIKE', '%C%')
                                   ->orWhere('maktab', 'LIKE', '%C%')
                                   ->orWhere('camp_zone', 'LIKE', '%ZONE 5%');
                            });
                    });
                } elseif ($maktabFilter === 'combo') {
                    $bq->where(function ($sub) {
                        $sub->where('camp', 'LIKE', '%Combo%')
                            ->orWhere('camp', 'LIKE', '%A & C%')
                            ->orWhereHas('package', function ($pq) {
                                $pq->where('camp_category', 'LIKE', '%Combo%')
                                   ->orWhere('camp_category', 'LIKE', '%A & C%')
                                   ->orWhere('camp_category', 'LIKE', '%C /A%');
                            });
                    });
                } else {
                    $term = '%' . $maktabFilter . '%';
                    $bq->where('camp', 'LIKE', $term)
                       ->orWhereHas('package', function ($pq) use ($term) {
                           $pq->where('camp_category', 'LIKE', $term)
                              ->orWhere('maktab', 'LIKE', $term)
                              ->orWhere('camp_zone', 'LIKE', $term);
                       });
                }
            });
        }

        // 2. Gender filter (Male / Female / All)
        if (!empty($genderFilter) && $genderFilter !== 'all') {
            $g = strtolower(trim($genderFilter));
            $query->whereHas('booking.persons', function ($pq) use ($g) {
                $pq->whereRaw('LOWER(TRIM(gender)) = ?', [$g]);
            });
        }

        // Order by check_in date ascending
        $hotelStays = $query->orderBy('check_in', 'asc')->get();

        // Calculate KPI Statistics
        $totalStays = $hotelStays->count();
        $totalRooms = $hotelStays->sum('no_of_rooms');

        $allBookingIds = $hotelStays->pluck('booking_id')->unique();

        $pilgrimQuery = BookingPerson::whereIn('booking_id', $allBookingIds);
        if (!empty($genderFilter) && $genderFilter !== 'all') {
            $pilgrimQuery->whereRaw('LOWER(gender) = ?', [strtolower($genderFilter)]);
        }
        $totalPilgrims = $pilgrimQuery->count();
        if ($totalPilgrims === 0 && $totalStays > 0 && ($genderFilter === 'all' || empty($genderFilter))) {
            $totalPilgrims = Booking::whereIn('id', $allBookingIds)->sum('no_of_pax');
        }

        $today = Carbon::today()->format('Y-m-d');
        $todayCheckins = (clone $query)->whereDate('check_in', $today)->count();
        $upcoming7Days = (clone $query)->whereDate('check_in', '>=', $today)
            ->whereDate('check_in', '<=', Carbon::today()->addDays(7)->format('Y-m-d'))
            ->count();
        $activeInHouse = (clone $query)->whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>=', $today)
            ->count();

        // Breakdown for badges
        $totalMalePilgrims = BookingPerson::whereIn('booking_id', $allBookingIds)
            ->whereRaw('LOWER(gender) = ?', ['male'])->count();
        $totalFemalePilgrims = BookingPerson::whereIn('booking_id', $allBookingIds)
            ->whereRaw('LOWER(gender) = ?', ['female'])->count();

        // Distinct camps from DB to populate dropdown
        $distinctCamps = Booking::whereNotNull('camp')->where('camp', '!=', '')->distinct()->pluck('camp')->toArray();

        return view('reports.maktab', compact(
            'hotelStays',
            'totalStays',
            'totalRooms',
            'totalPilgrims',
            'totalMalePilgrims',
            'totalFemalePilgrims',
            'todayCheckins',
            'upcoming7Days',
            'activeInHouse',
            'maktabFilter',
            'genderFilter',
            'distinctCamps'
        ));
    }
}
