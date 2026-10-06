<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingHotel;
use App\Models\BookingPerson;
use App\Models\Hotel;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckInReportController extends Controller
{
    public function index(Request $request)
    {
        $hotelFilter   = $request->get('hotel_name');
        $locationFilter = $request->get('location');
        $packageFilter = $request->get('package_id');
        $search        = $request->get('search');
        $quickFilter   = $request->get('quick_filter', 'all');
        $fromDate      = $request->get('from_date');
        $toDate        = $request->get('to_date');

        // Apply quick filter presets if from_date/to_date not manually set
        $today = Carbon::today()->format('Y-m-d');
        if ($quickFilter === 'today' && empty($fromDate) && empty($toDate)) {
            $fromDate = $today;
            $toDate   = $today;
        } elseif ($quickFilter === 'tomorrow' && empty($fromDate) && empty($toDate)) {
            $fromDate = Carbon::tomorrow()->format('Y-m-d');
            $toDate   = Carbon::tomorrow()->format('Y-m-d');
        } elseif ($quickFilter === 'this_week' && empty($fromDate) && empty($toDate)) {
            $fromDate = $today;
            $toDate   = Carbon::today()->addDays(7)->format('Y-m-d');
        } elseif ($quickFilter === 'this_month' && empty($fromDate) && empty($toDate)) {
            $fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $toDate   = Carbon::now()->endOfMonth()->format('Y-m-d');
        } elseif ($quickFilter === 'upcoming' && empty($fromDate) && empty($toDate)) {
            $fromDate = $today;
        }

        // Query BookingHotel records with active bookings
        $query = BookingHotel::with(['booking.client', 'booking.company', 'booking.package', 'booking.persons'])
            ->whereHas('booking', function ($q) {
                $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            });

        // Hotel name filter
        if (!empty($hotelFilter)) {
            $query->where('hotel_name', 'LIKE', '%' . $hotelFilter . '%');
        }

        // Location filter
        if (!empty($locationFilter) && $locationFilter !== 'all') {
            $query->where('location', $locationFilter);
        }

        // Check-in date range
        if (!empty($fromDate)) {
            $query->whereDate('check_in', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('check_in', '<=', $toDate);
        }

        // Package filter
        if (!empty($packageFilter)) {
            $query->whereHas('booking', function ($q) use ($packageFilter) {
                $q->where('package_id', $packageFilter);
            });
        }

        // Search filter (pilgrim name, passport, voucher, client name)
        if (!empty($search)) {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('hotel_name', 'LIKE', $term)
                  ->orWhereHas('booking', function ($bq) use ($term) {
                      $bq->where('voucher_number', 'LIKE', $term)
                         ->orWhere('care_of', 'LIKE', $term)
                         ->orWhereHas('client', function ($cq) use ($term) {
                             $cq->where('name', 'LIKE', $term)
                                ->orWhere('passport_number', 'LIKE', $term)
                                ->orWhere('phone', 'LIKE', $term);
                         })
                         ->orWhereHas('company', function ($comQ) use ($term) {
                             $comQ->where('name', 'LIKE', $term);
                         })
                         ->orWhereHas('persons', function ($pq) use ($term) {
                             $pq->where('full_name', 'LIKE', $term)
                                ->orWhere('surname', 'LIKE', $term)
                                ->orWhere('given_name', 'LIKE', $term)
                                ->orWhere('passport_number', 'LIKE', $term)
                                ->orWhere('cnic', 'LIKE', $term);
                         });
                  });
            });
        }

        // Order by check_in date ascending
        $hotelStays = $query->orderBy('check_in', 'asc')->get();

        // Calculate KPI Statistics
        $totalStays = $hotelStays->count();
        $totalRooms = $hotelStays->sum('no_of_rooms');
        
        $allBookingIds = $hotelStays->pluck('booking_id')->unique();
        $totalPilgrims = BookingPerson::whereIn('booking_id', $allBookingIds)->count();
        if ($totalPilgrims === 0 && $totalStays > 0) {
            $totalPilgrims = Booking::whereIn('id', $allBookingIds)->sum('no_of_pax');
        }

        $todayCheckins = BookingHotel::whereDate('check_in', $today)->count();
        $upcoming7Days = BookingHotel::whereDate('check_in', '>=', $today)
            ->whereDate('check_in', '<=', Carbon::today()->addDays(7)->format('Y-m-d'))
            ->count();
        $activeInHouse = BookingHotel::whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>=', $today)
            ->count();

        // Dropdown Data
        $registeredHotels = Hotel::orderBy('name')->get();
        $distinctHotelNames = BookingHotel::select('hotel_name')->distinct()->whereNotNull('hotel_name')->pluck('hotel_name');
        $packages = Package::latest()->get();

        return view('reports.check_in', compact(
            'hotelStays',
            'totalStays',
            'totalRooms',
            'totalPilgrims',
            'todayCheckins',
            'upcoming7Days',
            'activeInHouse',
            'registeredHotels',
            'distinctHotelNames',
            'packages',
            'hotelFilter',
            'locationFilter',
            'packageFilter',
            'search',
            'quickFilter',
            'fromDate',
            'toDate'
        ));
    }
}
